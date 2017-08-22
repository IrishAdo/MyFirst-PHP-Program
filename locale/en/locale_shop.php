<?php
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Define the locales for this module
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/

if (!defined("LOADED_SHOP_LOCALE")){
	define ("LOADED_SHOP_LOCALE"							, "1");
	define ("LOCALE_SHOP_HOW_TO_PAY"						, "How would you like to pay for your order");
	define ("LOCALE_INVOICE"								, "Cheque");
	define ("LOCALE_CREDIT_CARD"							, "Credit Card");
	define ("LOCALE_CHOOSE_PAYMENT"							, "Payment Methods");
	define ("LOCALE_SALES_TAX"								, "Sales Tax");
	define ("MANAGE_SHOP_MESSAGES"							, "Feedback Messages");
	define ("MANAGE_PAYGATE"								, "Define Payment Gateway Parameters");
	define ("LOCALE_CONFIRM_MSG_TAB"						, "Credit Card Approved");
	define ("LOCALE_DENY_MSG_TAB"							, "Credit Card Rejected");
	define ("MANAGE_SHOP_DELIVERY_SETTINGS"					, "Delivery Settings");
	define ("LOCALE_INVOICE_MSG_TAB"						, "Invoice Message");
	define ("LOCALE_INVOICE_EMAIL_MSG_TAB"					, "Invoice Email");
	define ("LOCALE_MESSAGES_UPDATED"						, "Messages Updated");
	define ("LOCALE_MESSAGES_UPDATED_TXT"					, "Thankyou the messages have been updated");
	define ("LOCALE_CHARGE_SALES_TAX"						, "Is sales tax charged in this shop");
	define ("LOCALE_PERCENT_SALES_TAX"						, "If Yes, what percentage is your sales tax");
	define ("LOCALE_ALWAYS_CHARGE"							, "Should sales tax always be charged (no exceptions)");
	define ("LOCALE_SHOP_CAN_REQUEST_INVOICE"				, "Can a user request a paper invoice");
	define ("LOCALE_SHOP_SEND_PAPER_INVOICE"				, "Request a paper invoice");
	if(!defined("MANAGE_PAYMENT_ORDERS")){
		define ("MANAGE_PAYMENT_ORDERS"						, "Manage payment orders");
	}
	define("LOCALE_SHOP_PAYMENT_MESSAGES"					, "Payment messages");
	define("LOCALE_SHOP_SAVE_SALES_MSG"						, "Thankyou your Sales TAX (VAT) settings have been updated");
	define("LOCALE_SHOP_SAVE_SALES_LABEL"					, "Setup Confirm");
	define("LOCALE_SHOP_SAVE_SALES_HEADER"					, "Sales Tax Settings - Confirmation");
	/*************************************************************************************************************************
    *  shipping matrix
    *************************************************************************************************************************/
	define ("MANAGE_CARRIAGE_COSTS"							, "Carriage Costs");
	define ("MANAGE_DELIVER_TO"								, "Deliver To");
	define ("MANAGE_SHOP_DELIVERY_SETTINGS_CONFIRM_LABEL"	, "Delivery Setup Confirm");
	define ("MANAGE_SHOP_DELIVERY_SETTINGS_CONFIRM_MSG"		, "Thankyou your delivery settings have been updated");
	define ("LOCALE_SHOP_SAME_DELIVERY"						, "Should the delivery address always be the same as the bill payers");
	/*************************************************************************************************************************
    * Payment Settings
    *************************************************************************************************************************/
	define("LOCALE_SHOP_CAN_INVOICE"						, "Can customers request to pay by cheque");
	define("LOCALE_SHOP_GATEWAYS"							, "List of Payment Gateways to choose form");
	/*************************************************************************************************************************
    *
    *************************************************************************************************************************/
	define ("MANAGEMENT_SHOP", "Shop Manager");
	define ("MANAGE_SHOP_STOCK", "Stock Manager");
	define ("MANAGE_SHOP_ORDERS", "Order Manager");
	define ("LOCALE_PAGE_ASSOCIATED", "Associated with these pages.");
	define ("LOCALE_SHOP_STOCK_LABEL", "Stock Label");
	define ("LOCALE_SHOP_STOCK_DISCOUNT", "Discount");
	define ("LOCALE_SHORT_DESCRIPTION", "Short Description");
	define ("LOCALE_SHOP_STOCK_PRICE", "Price");
	define ("LOCALE_SHOP_STOCK_STATUS", "Status");
	define ("LOCALE_SHOP_STOCK_IMAGE", "Image");
	define ("LOCALE_SHOP_STOCK_HOT", "Hot Product");
	define ("LOCALE_IMAGE_FILES_ASSOCIATED", "Associate an image with this product");
	define ("LOCALE_SELECT_SHOP_STOCK_STATUS", "Please select whether this item is available on the site.");
	define ("LOCALE_SHOP_STOCK_HOT_DESCRIBE", "Mark this as a hot product");
	define ("LOCALE_SHOP_STOCK_PRICE_DISCOUNT", "Price / Discounts");
	define ("LOCALE_SHOP_STOCK_DISCOUNT_PERCENT", "Discount Percent");
	define ("LOCALE_SHOP_STOCK_DISCOUNT_CASH", "Discount Cash");
	define ("LOCALE_OR", "or");
	define ("LOCALE_SHOP_STOCK_REMOVAL", "Removal of stock from system.");
	define ("LOCALE_SHOP_ORDERS", "Total number of orders grouped by their processing status");
	define ("MANAGE_SHOP_LOCATION", "Shop site location");
	define ("LOCALE_SHOP_LOCATION_FORM", "Where do you want to position your shop on your site.");
	define ("LOCALE_SHOP_SELECT_LOACTION", "Select the menu location your shop will appear");
	define ("LOCALE_SHOP_NO_DISPLAY", "Do not display a shop on the site at all");
	define ("LOCALE_SHOP_LOST_BASKETS", "Number of baskets marked as discarded (24 hrs or more old).");
	define ("LOCALE_SORRY_NO_LOST_BASKETS", "There are currently no discarded baskets in the system.");

	define ("LOCALE_SHOP_BASKET_IN_USE"							, "Currently in use");
	define ("LOCALE_SHOP_BASKET_AT_CHECKOUT"					, "Currently in purchasing mode");
	define ("LOCALE_SHOP_BASKET_REQUESTING_PAYMENT"				, "Requesting Payment");
	define ("LOCALE_SHOP_BASKET_PURCHASE_COMPLETE"				, "Purchasing Complete");
	define ("LOCALE_SHOP_BASKET_PURCHASE_BEING_PROCESSED"		, "Processing");
	define ("LOCALE_SHOP_BASKET_PURCHASE_BEING_DELIVERED"		, "Shipping");
	define ("LOCALE_SHOP_BASKET_PURCHASE_DELIVERED"				, "Delivered");
	define ("LOCALE_SHOP_BASKET_PURCHASE_REJECTED"				, "Rejected");
	define ("LOCALE_SHOP_BASKET_READY_DELIVERY"					, "Ready for delivery");
	define ("LOCALE_SHOP_BASKET_PURCHASE_OUT_OF_STOCK"			, "Out of stock");
	define ("LOCALE_SHOP_BASKET_PURCHASE_PROCESSED"				, "Number of baskets processed");
	define ("LOCALE_SHOP_BASKET_PURCHASE_IN_DELIVERY"			, "Number of baskets marked as in delivery");
	define ("LOCALE_SHOP_BASKET_SAVED"							, "Number of baskets currently saved in system");
	define ("LOCALE_SHOP_PROCESS_ORDER"							, "Process this order");
	define ("LOCALE_SHOP_DELETE_ORDER"							, "Delete this order");
	define ("LOCALE_SHOP_NUM_OF_ITEMS"							, "Items in Basket");
	define ("LOCALE_SHOP_PLACE_LOCATION_CONFIRM"				, "<p>The display location of the shop has now been updated</p>");
	define ("LOCALE_SHOP_VIEW_BASKET"							, "View basket");
	define ("LOCALE_SHOP_BUY_NOW"								, "Buy Now");
	define ("LOCALE_SHOP_TOTAL_ORDERS"							, "Total number of Baskets listed");
	define ("LOCALE_ZERO_PRICE_AUTO_REDUCE"						, "Auto reduce quantity on baskets that are free");
}


?>