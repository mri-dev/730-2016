<?
namespace PortalManager;

/**
* class Giftcards
* @package PortalManager
*/
class Giftcard
{
  private $db = null;
  public $shop = null;
  private $cart = null;

	function __construct( $arg = array() )
	{
    $this->db   = $arg['db'];
    $this->shop = $arg['shop'];
    // Kosár
    $this->cart = $this->shop->cartInfo(\Helper::getMachineID());

		return $this;
	}

  public function activate( $code = false, $secure = false)
  {
    $mid = \Helper::getMachineID();

    if (!$mid) {
      throw new \Exception(__("Engedélyezze a sütik (cooki) használatát, majd frissítse le az oldalt."));
    }

    if ($this->cart[itemNum] == 0) {
      throw new \Exception(__("Az Ön kosara jelenleg üres. Helyezzen termékeket a kosarába és utána aktiválja ajándékkártyáit."));
    }

    if ( !$this->checkAvaiable($code) ) {
      throw new \Exception(sprintf(__("Az aktiválni kívánt kártya (#%s) nem használható vagy már nem érvényes."), $code));
    }

    if ( $this->isInUsed($code) ) {
      throw new \Exception(__("A megadott ajándékkártyát már megadta."));
    }

    $secure = trim($secure);
    if ( strlen($secure) != 3 ) {
      throw new \Exception(__("A biztonsági kódnak 3 karakternek kell lennie."));
    }

    $this->db->query("INSERT
    INTO giftcard_using(sessionid, code, verify_code)
    VALUES($mid, $code, '$secure');");
  }

  public function getAddedCodes()
  {
    $mid = \Helper::getMachineID();

    if (empty( $mid )) {
      return false;
    }

    $codes = array();
    $back = array();

    $q = "SELECT
      gu.ID, gu.code, gu.verify_code,
      g.amount_".\Lang::getPriceCode()." as price
    FROM giftcard_using as gu
    LEFT OUTER JOIN giftcards as g ON g.code = gu.code
    WHERE
      gu.sessionid = '$mid' and gu.orderID = 0";
    $qq = $this->db->query($q);

    $list = $qq->fetchAll(\PDO::FETCH_ASSOC);

    $total_price = 0;
    foreach ($list as $l) {
      $codes[] = $l;
      $total_price += $l['price'];
    }

    $back['num'] = count($codes);
    $back['total_price'] = $total_price;
    $back['valuta'] = \Lang::getPriceCode();
    $back['codes'] = $codes;

    return $back;
  }

  private function isInUsed( $code )
  {
    $mid = \Helper::getMachineID();

    if (empty( $mid )) {
      return false;
    }
    $q = "SELECT ID FROM giftcard_using WHERE sessionid = '$mid' and code = '$code' and orderID = 0;";
    $qq = $this->db->query($q);

    if ($qq->rowCount() != 0) {
      return true;
    }
    return false;
  }

  private function checkAvaiable( $code )
  {
    if (!$code) {
      return false;
    }

    $q = "SELECT ID FROM giftcards WHERE code = '$code' and when_used IS NULL and expired > now();";
    $qq = $this->db->query($q);

    if ($qq->rowCount() != 0) {
      return true;
    } else {
      return false;
    }
  }

  public function __destruct()
  {
    $this->db = null;
    $this->shop = null;
  }
}
?>
