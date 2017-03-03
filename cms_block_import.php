<?php

require_once '../app/Mage.php'; 
Mage::app();

/**
 * Import CMS Static Blocks from CSV
 *
 * @author     Timur Allashev <timka@ua.fm>
 */

$blocks = file('import_blocks.csv'); // Path to CSV file
    
if($blocks){
    
    $resource = Mage::getSingleton('core/resource');
    $readConnection = $resource->getConnection('core_read');
    $writeConnection = $resource->getConnection('core_write');
    
    $all_blocks = [];

      foreach($blocks as $block)
      {
        $str_arr = str_getcsv($block, ";");
        $all_blocks[] = $str_arr;
      }
  
    foreach($all_blocks as $val) {
        
        $set_block = "INSERT INTO cms_block (`title`, `identifier`, `content`, `creation_time`, `update_time`, `is_active`) VALUES ('$val[0]', '$val[2]', '$val[3]', NOW(), NOW(), 1);";
        $writeConnection->query($set_block); // Insert new Static block into the table
      
        $store_id = $val[1]; // Get the ID of store view for the static block
        
      
        $getId = "SELECT `block_id` FROM cms_block ORDER BY `block_id` DESC LIMIT 1;";
        $id_arr = $readConnection->query($getId)->fetchAll(); 
        $id = $id_arr[0]['block_id']; // Get the ID of last item in the cms_block table
      
        $setStore_id = "INSERT INTO cms_block_store (`block_id`,`store_id`) VALUES ('$id','$store_id');"; 
        $writeConnection->query($setStore_id); // Adding new string to the cms_block_store table with store view ID for new static block
    }
    echo 'New static blocks were successfuly created';
}else{
    echo "File connection ERROR";
}