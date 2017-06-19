 <?php
class Md_Customdiscount_Model_Observer
{
	const XML_PATH_MODULE_ENABLE    = 'customdiscount/customdiscount_group/enable';
    const XML_PATH_DISCOUNT_AMOUNT  = 'customdiscount/customdiscount_group/discount';
	public function setDiscount($observer)
    {
		if(Mage::getStoreConfig(self::XML_PATH_MODULE_ENABLE)){
            $quote = $observer->getEvent()->getQuote();
            $quoteid = $quote->getId();
            $discountAmount = Mage::getStoreConfig(self::XML_PATH_DISCOUNT_AMOUNT);
            if ($quoteid) {
                if ($discountAmount > 0) {
                    $total = $quote->getBaseSubtotal();
                    $quote->setSubtotal(0);
                    $quote->setBaseSubtotal(0);

                    $quote->setSubtotalWithDiscount(0);
                    $quote->setBaseSubtotalWithDiscount(0);

                    $quote->setGrandTotal(0);
                    $quote->setBaseGrandTotal(0);


                    $canAddItems = $quote->isVirtual() ? ('billing') : ('shipping');
                    foreach ($quote->getAllAddresses() as $address) {

                        $address->setSubtotal(0);
                        $address->setBaseSubtotal(0);

                        $address->setGrandTotal(0);
                        $address->setBaseGrandTotal(0);

                        $address->collectTotals();

                        $quote->setSubtotal((float) $quote->getSubtotal() + $address->getSubtotal());
                        $quote->setBaseSubtotal((float) $quote->getBaseSubtotal() + $address->getBaseSubtotal());

                        $quote->setSubtotalWithDiscount(
                                (float) $quote->getSubtotalWithDiscount() + $address->getSubtotalWithDiscount()
                        );
                        $quote->setBaseSubtotalWithDiscount(
                                (float) $quote->getBaseSubtotalWithDiscount() + $address->getBaseSubtotalWithDiscount()
                        );

                        $quote->setGrandTotal((float) $quote->getGrandTotal() + $address->getGrandTotal());
                        $quote->setBaseGrandTotal((float) $quote->getBaseGrandTotal() + $address->getBaseGrandTotal());

                        $quote->save();

                        $quote->setGrandTotal($quote->getBaseSubtotal() - $discountAmount)
                                ->setBaseGrandTotal($quote->getBaseSubtotal() - $discountAmount)
                                ->setSubtotalWithDiscount($quote->getBaseSubtotal() - $discountAmount)
                                ->setBaseSubtotalWithDiscount($quote->getBaseSubtotal() - $discountAmount)
                                ->save();


                        if ($address->getAddressType() == $canAddItems) {
                            //echo $address->setDiscountAmount; exit;
                            $address->setSubtotalWithDiscount((float) $address->getSubtotalWithDiscount() - $discountAmount);
                            $address->setGrandTotal((float) $address->getGrandTotal() - $discountAmount);
                            $address->setBaseSubtotalWithDiscount((float) $address->getBaseSubtotalWithDiscount() - $discountAmount);
                            $address->setBaseGrandTotal((float) $address->getBaseGrandTotal() - $discountAmount);
                            if ($address->getDiscountDescription()) {
                                $address->setDiscountAmount(-($address->getDiscountAmount() - $discountAmount));
                                $address->setDiscountDescription($address->getDiscountDescription() . ', Custom Discount');
                                $address->setBaseDiscountAmount(-($address->getBaseDiscountAmount() - $discountAmount));
                            } else {
                                $address->setDiscountAmount(-($discountAmount));
                                $address->setDiscountDescription('Custom Discount');
                                $address->setBaseDiscountAmount(-($discountAmount));
                            }
                            $address->save();
                        }//end: if
                    } //end: foreach
                    //echo $quote->getGrandTotal();

                    foreach ($quote->getAllItems() as $item) {
                        //We apply discount amount based on the ratio between the GrandTotal and the RowTotal
                        $rat = $item->getPriceInclTax() / $total;
                        $ratdisc = $discountAmount * $rat;
                        $item->setDiscountAmount(($item->getDiscountAmount() + $ratdisc) * $item->getQty());
                        $item->setBaseDiscountAmount(($item->getBaseDiscountAmount() + $ratdisc) * $item->getQty())->save();
                    }
                }
            }
        }
    }

} 