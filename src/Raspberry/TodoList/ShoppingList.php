<?php

namespace Raspberry\TodoList;

/**
 * @Service(public=false)
 */
class ShoppingList {

	/**
	 * @var ShoppingListGateway
	 */
	private $gateway;

	/**
	 * @Inject("@ShoppingListGateway")
	 * @param ShoppingListGateway $shopping_list_gateway
	 */
	public function __construct(ShoppingListGateway $shopping_list_gateway) {
		$this->gateway = $shopping_list_gateway;
	}

	/**
	 * @return string[]
	 */
	public function getShoppingListItems() {
		return $this->gateway->getShoppingListItems();
	}

	/**
	 * @param string $name
	 */
	public function addShoppingListItem($name) {
		$this->gateway->addShoppingListItem($name);
	}

	/**
	 * @param string $name
	 */
	public function removeShoppingListItem($name) {
		$this->gateway->removeShoppingListItem($name);
	}

}
