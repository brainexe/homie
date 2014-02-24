<?php

namespace Raspberry\Dashboard\Widgets;
use Matze\Core\Traits\TranslatorTrait;
use Raspberry\Dashboard\AbstractWidget;

/**
 * @Service(public=false, tags={{"name" = "widget"}})
 */
class TimeWidget extends AbstractWidget {

	use TranslatorTrait;

	const TYPE = 'time';

	/**
	 * @return string
	 */
	public function renderWidget() {
		return $this->render('widgets/widget.html.twig', [
			'title' => $this->trans('Time'),
			'content' => date('c')
		]);
	}

	/**
	 * @return string
	 */
	public function getId() {
		return self::TYPE;
	}
}
