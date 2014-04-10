<?php

namespace Raspberry\Twig\Extensions;

use Twig_Extension;

/**
 * @TwigExtension
 */
class PiwikExtension extends Twig_Extension {

	/**
	 * @var string
	 */
	private $_piwik_site;

	/**
	 * @var integer
	 */
	private $_piwik_id;

	/**
	 * @Inject({"%piwik.site%", "%piwik.id%"});
	 */
	public function __construct($piwik_site, $piwik_id) {
		$this->_piwik_site = $piwik_site;
		$this->_piwik_id = $piwik_id;
	}

	/**
	 * {@inheritdoc}
	 */
	function getName() {
		return 'piwik';
	}

	public function getFunctions() {
		return [
			'piwik' => new \Twig_Function_Method($this, 'piwik', ['is_safe' => ['all']])
		];
	}

	/**
	 * @return string
	 */
	public function piwik() {
		if (empty($this->_piwik_site) || empty($this->_piwik_id)) {
			return '';
		}

		return sprintf('
			<script type="text/javascript">
			  var _paq = _paq || [];
			  _paq.push(["trackPageView"]);
			  _paq.push(["enableLinkTracking"]);

			  (function() {
				var u=(("https:" == document.location.protocol) ? "https" : "http") + "://%s/";
				_paq.push(["setTrackerUrl", u+"piwik.php"]);
				_paq.push(["setSiteId", "%s"]);
				var d=document, g=d.createElement("script"), s=d.getElementsByTagName("script")[0]; g.type="text/javascript";
				g.defer=true; g.async=true; g.src=u+"piwik.js"; s.parentNode.insertBefore(g,s);
			  })();
			</script>', $this->_piwik_site, $this->_piwik_id);

	}


}
