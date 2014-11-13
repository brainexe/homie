<?php

namespace Raspberry\TodoList;

/**
 * @Service(public=false)
 */
class ShoppingList {

	/**
	 * @var ShoppingListGateway
	 */
	private $_shopping_list_gateway;

	/**
	 * @Inject("@ShoppingListGateway")
	 * @param ShoppingListGateway $shopping_list_gateway
	 */
	public function __construct(ShoppingListGateway $shopping_list_gateway) {
		$this->_shopping_list_gateway = $shopping_list_gateway;
	}

	/**
	 * @return string[]
	 */
	public function getShoppingListItems() {
		return $this->_shopping_list_gateway->getShoppingListItems();
	}

	/**
	 * @param string $name
	 */
	public function addShoppingListItem($name) {
		$this->_shopping_list_gateway->addShoppingListItem($name);
	}

	/**
	 * @param string $name
	 */
	public function removeShoppingListItem($name) {
		$this->_shopping_list_gateway->removeShoppingListItem($name);
	}

} 