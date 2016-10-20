<?php

defined('_JEXEC') or die;

/**
 * Plugin that permits to use shortcodes in content
 * that are transformed in something usefull, based on regexp
 */
class PlgContentRewidgets extends JPlugin
{
	/* shortcodes definitions */
	protected static $shortCodes = array();

	const DEFS_DELIMITER = "====";
	const PARTS_DELIMITER = "----";

	const ARG_PATTERN = "/(\w+)=\"([^\"]*)\"/";
	const DEF_PATTERN = "/{(\w+)(?:\s+\w+=\"[^\"]*\")*\s*}/";

	/**
	 * given a string shortcode {foo bar="baz"}, gives code ans args
	 * 
	 * @return code and an array association keys to values
	 */
	protected static function strDefinition(&$strDef)
	{
		$matches = array();
		preg_match(self::DEF_PATTERN, $strDef, $matches);
		$code = $matches[1];
		$matches = array();
		preg_match_all(self::ARG_PATTERN, $strDef, $matches, PREG_SET_ORDER);
		$args = array();
		foreach($matches as $match) {
			$args[$match[1]] = $match[2];
		}
		return array($code, $args);
	}

	/**
	 * initial load of settings : short code definitions
	 */
	protected static function loadShortcodesDefinitions(&$params)
	{
		if (!empty(self::$shortCodes)) {
			return;  // already loaded
		}
		$raw = $params->def("definitions", "");
		foreach (explode(self::DEFS_DELIMITER, $raw) as $rawDef) {
			if (!empty($rawDef)) {
				list($shortCode, $template) = explode(self::PARTS_DELIMITER, $rawDef);
				list($code, $args) = self::strDefinition($shortCode);
				$pattern =  '/{' . $code . '\s+[^}]*}/i';
				self::$shortCodes[$code] = array(
					'pattern' => $pattern,
					'args' => $args,
					'template' => $template);
			}
		}
	}

	/**
	 * callback for shortcode replacement
	 *
	 * compute the replacement and return it
	 */
	protected function codeReplace($matches)
	{
		$strDef = $matches[0];
		list($code, $args) = self::strDefinition($strDef);
		if (array_key_exists($code, self::$shortCodes)) {
			$def = self::$shortCodes[$code];
			$args = array_merge($def["args"], $args);
			$template = $def["template"];
			foreach ($args as $name => $value) {
				$template = str_replace("{".$name."}", $value, $template);
			}
			return $template;
		} else {
			return $matches[0];  // unknown shortcode, no change
		}
	}

	/**
	 * replace short codes with computed values
	 *
	 * @param   string   $context   The context of the content being passed to the plugin.
	 * @param   object   &$article  The article object.  Note $article->text is also available
	 * @param   mixed    &$params   The article params
	 * @param   integer  $page      The 'page' number
	 *
	 * @return  mixed   true if there is an error. Void otherwise.
	 *
	 */
	public function onContentPrepare($context, &$article, &$params, $page = 0)
	{
		// Don't run this plugin when the content is being indexed
		if ($context == 'com_finder.indexer') {
			return true;
		}

		self::loadShortcodesDefinitions($this->params);

		$article->text = preg_replace_callback(
		    self::DEF_PATTERN,
		    function ($matches) {
			  return $this->codeReplace($matches);
			},
			$article->text);
		return true;
	}

}
