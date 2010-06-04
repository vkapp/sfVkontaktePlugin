<?
/**
 * sfVkontakteActions actions class.
 *
 * @package	sfVkontaktePlugin
 * @subpackage actions
 * @author	 Alexey Tyagunov <atyaga@gmail.com>
 */
class sfVkontakteActions extends sfActions {
	/**
	 * @return
	 */
	public function preExecute() {
		$userModelTable = sfConfig::get('app_vkontakte_user_model');
		if ($this->getUser()->id) {
			// get or create model user
			$this->user = Doctrine_Core::getTable($userModelTable)->findOneById($this->getUser()->id);
			if (!$this->user) {
				$this->user = new $userModelTable();
				$this->user->id = $this->getUser()->id;
				$this->user->save();
			}
			// if we need to fetch profiles
			$this->getUser()->need_fetch = $this->user->fetched_at < date('Y-m-d');
		}

		// add JS to response
		sfContext::getInstance()->getResponse()->addJavascript('http://vkontakte.ru/js/api/xd_connection.js?2', 'first');
		sfContext::getInstance()->getResponse()->addJavascript('/sfVkontaktePlugin/js/common.js', 'first');

		parent::preExecute();
	}

	public function returnJSON($data) {
		$json = json_encode($data);

		if (sfConfig::get('sf_debug') && !$this->getRequest()->isXmlHttpRequest()) {
			$this->getContext()->getConfiguration()->loadHelpers('Partial');
			$this->renderText(get_partial('sfVkontakteFetch/jsonDebug', array('data' => $data , 'json' => $json)));
		} else {
			$this->getResponse()->setHttpHeader('Content-type', 'application/json');
			$this->renderText($json);
		}
		return sfView::NONE;
	}
}