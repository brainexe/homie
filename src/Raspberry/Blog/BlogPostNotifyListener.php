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

	const NOTIFY_TIME = '19:55';

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

		$hour = date('G');
		$minute = (int)date('i');
		$text = sprintf('%s hat um %d Uhr %d geschrieben: %s.', $speak_event->user_vo->username, $hour, $minute, $post->text);

		if ($post->mood) {
			$text .= sprintf(' Stimmung: %d von %d', $post->mood, BlogPostVO::MAX_MOOD);
		}

		$espeak = new EspeakVO($text);
		$speak_event = new EspeakEvent($espeak);

		// speak NOW and today at next defined time
		$this->dispatchInBackground($speak_event);

		$time = strtotime(self::NOTIFY_TIME);
		if ($time > time()) {
			$this->dispatchInBackground($speak_event, $time);
		}

	}
}