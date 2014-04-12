<?php

namespace Raspberry\Blog;

use Matze\Core\EventDispatcher\AbstractEventListener;
use Matze\Core\Traits\EventDispatcherTrait;
use Raspberry\Blog\Events\BlogEvent;
use Raspberry\Espeak\EspeakEvent;
use Raspberry\Espeak\EspeakVO;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @EventListener
 */
class BlogPostNotifyListener extends AbstractEventListener {

	const NOTIFY_TIME = '18:00';

	use EventDispatcherTrait;

	/**
	 * {@inheritdoc}
	 */
	public static function getSubscribedEvents() {
		return [
			BlogEvent::POST=> 'handlePostEvent'
		];
	}

	/**
	 * @param BlogEvent $speak_event
	 */
	public function handlePostEvent(BlogEvent $speak_event) {
		$post = $speak_event->post;

		$text = sprintf('%s hat geschrieben: %s.', $speak_event->user_vo->username,$post->text);

		if ($post->mood) {
			$text .= sprintf(' Stimmung: %d von %d', $post->mood, BlogPostVO::MAX_MOOD);
		}

		$espeak = new EspeakVO($text);
		$speak_event = new EspeakEvent($espeak);

		$time = strtotime(self::NOTIFY_TIME);
		if ($time < time()) {
			$time += 86400;
		}

		// speak NOW and at next defined time
		$this->dispatchInBackground($speak_event);
		$this->dispatchInBackground($speak_event, $time);
	}
}