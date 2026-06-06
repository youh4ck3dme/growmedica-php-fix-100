<?php
namespace Export;
class Heureka implements Implementacion{
    private $xml;
    private $db;
    private $count=0;
    private $images=array();
    private $categories=array();
    private $manufacturer=array();
    private $params=array();
    private $menu=array();

    public function __construct($db)
    {
        if($db instanceof \PDO){
            $this->db=$db;
        }else{
            throw new \ErrorException("No find db");
        }
        $root="SHOP";
        $this->xml=new \SimpleXMLElement("<?xml version=\"1.0\" encoding=\"utf-8\" ?><{$root}></{$root}>");
        $this->setMenu();
        $this->setImamages();
        $this->setCategories();
        $this->setManufacturer();
    }
    private function setMenu(){
        $query="SELECT menu_id as id FROM ".TABLE_PREFIX."menu WHERE 1";
        $menu= $this->db->query($query)->fetchAll(\PDO::FETCH_ASSOC);
        foreach($menu as $m){
            $this->menu[$m['id']]=\Menu::getHyperLinkByID($m['id']);
        }
    }
    private function setImamages(){
        $query="SELECT photo_category_id as id,src FROM ".TABLE_PREFIX."_photo_images WHERE 1";
        $images= $this->db->query($query)->fetchAll(\PDO::FETCH_ASSOC);
        foreach($images as $image){
            $this->images[$image['id']][]=$image['src'];
        }

    }
    private function setManufacturer(){
        $query="SELECT manufacturer_id as id,sk_name as name FROM ".TABLE_PREFIX."manufacturer WHERE 1";
        $manufacturer= $this->db->query($query)->fetchAll(\PDO::FETCH_ASSOC);
        foreach($manufacturer as $m){
            $this->manufacturer[$m['id']]=$m['name'];
        }
    }
    private function setCategories(){
        $query="SELECT  p.product_id as id,m.sk_name as name,m.sk_name_seo as seo,m.menu_id as menu_id,m.heureka_category_name
                FROM ".TABLE_PREFIX."menu m
                JOIN ".TABLE_PREFIX."product_menu p ON (p.menu_id=m.menu_id)
                WHERE 1";

        $categories= $this->db->query($query)->fetchAll(\PDO::FETCH_ASSOC);
        $i=0;
        foreach($categories as $category){
            $this->categories[$category['id']][$i]['name']=$category['name'];
            $this->categories[$category['id']][$i]['name_seo']=\Menu::getHyperLinkByID($category['menu_id']);;
            $this->categories[$category['id']][$i]['menu_id']=$category['menu_id'];
            $this->categories[$category['id']][$i]['heureka_category_name']=$category['heureka_category_name'];
            $i++;
        }

    }
    public function start()
    {
        $this->product();
    }
    private function product()
    {
        $query="SELECT sk_name,image_src,sk_name_seo,sk_description,price,manufacturer_id,product_id,code_ean as ean,delivery_time FROM ".TABLE_PREFIX."product WHERE  available='1' AND price!='0' ";
        $products=$this->db->query($query)->fetchAll(\PDO::FETCH_ASSOC);
        $delivery=$this->getDelivery();

        foreach($products as $product){
            $categories=$this->getProductCategory($product['product_id']);

            if($categories!=null)
                $category=reset($categories);
            else{
                continue;
            }
            $item=$this->xml->addChild("SHOPITEM");
            $item->addChild("ITEM_ID",$product['product_id']);
            $item->addChild("PRODUCTNAME",htmlspecialchars($product['sk_name']));
            $item->addChild("DESCRIPTION","<![CDATA[".htmlspecialchars($product["sk_description"])."]]>");
            $item->addChild("URL",htmlspecialchars($this->createProductUrl($category['menu_id'],$product['sk_name_seo'],$product['product_id'])));
            $item->addChild("IMGURL",$this->createImageUrl($product['image_src']));


            $images=$this->getProductImages($product['product_id']);
            if(is_array($images))
            foreach($images as $image){
                $item->addChild("IMGURL_ALTERNATIVE",$this->createImageUrl($image));
            }

            $item->addChild("PRICE_VAT",$product['price']);
            $item->addChild("MANUFACTURER",$this->getManufacturer($product['manufacturer_id']));
            $item->addChild("EAN",$product['ean']);
            $item->addChild("CATEGORYTEXT",$this->getCategory($product['product_id']));
            ($product['delivery_time'] == '1') ? $item->addChild("DELIVERY_DATE",'0'): $item->addChild("DELIVERY_DATE",'3');
			//$item->addChild("DELIVERY_DATE",'15');


            foreach((array) $delivery as $del){
                $deliveries=$item->addChild("DELIVERY");
                $deliveries->addChild("DELIVERY_ID",$del['hdt_name']);
                $deliveries->addChild("DELIVERY_PRICE",$del['price']);
               // $deliveries->addChild("DELIVERY_PRICE_COD",'5');
            }
            $this->count++;
        }
        $this->clear();
    }
    private function clear(){
        unset( $this->categories);
        unset( $this->menu);
        unset( $this->params);
        unset( $this->images);
        unset( $this->manufacturer);
    }
    private function getCategory($product_id){
        $categories=$this->getProductCategory($product_id);
        foreach($categories as $category){
            if(!empty($category['heureka_category_name']) && strlen($category['heureka_category_name'])!=0){
                return $category['heureka_category_name'];
            }
        }
    }
    private function getDeliveryDate($quantity_ready,$quntity_on_way,$days_till_arrival,$days_till_next_order){
        if($quantity_ready!='0' || $quantity_ready!=''){
            return 1;
        }else{
            if($quntity_on_way!='0' || $quntity_on_way!='') {

                return $days_till_arrival;
            }
            else{
               return $days_till_next_order;
            }
        }
    }
    private function getParams($id){
        return $this->params[$id];
    }
    private function getDelivery(){
        $query="SELECT name, hdt_name, price_eur as price FROM ".TABLE_PREFIX."delivery_type AS d
        LEFT JOIN " . TABLE_PREFIX . "heureka_delivery_type AS h ON(h.hdt_id = d.heureka_delivery_type_id) WHERE 1 GROUP BY hdt_name";
        return $this->db->query($query)->fetchAll(\PDO::FETCH_ASSOC);
    }
    private function createProductUrl($menu_id,$product_seo,$product_id){
        $category_url=$this->menu[$menu_id];
        return ROOTDIR."/".$category_url."/produkt/".$product_seo.'/'.$product_id;
    }
    private function createImageUrl($file_name){
            return ROOTDIR."/photos/original/".$file_name;
    }
    private function getManufacturer($manufacture_id){
        $ouput=$this->manufacturer[$manufacture_id];
        return $ouput;
    }
    private function getProductImages($id){
        $ouput=$this->images[$id];
        unset($this->images[$id]);
        return$ouput;
    }
    private function getProductCategory($id){
        $ouput=$this->categories[$id];
        return $ouput;
    }
    public function getSimpleXML()
    {
        return $this->xml;
    }

    public function getNumberExportItems()
    {
        return  $this->count;
    }

}