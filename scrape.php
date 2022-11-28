<?php
//Autoload composer dependencies
include 'vendor/autoload.php';

//use PHP dom parser library from paquettg.
use PHPHtmlParser\Dom;

/*
 * Simple function to accept URL of a webapge and get the dom to find elements.
 * Accepts String as parameter
 * Return boolean
 */
function scrape($url = null)
{

    //check if URL parameter is not null, return false if URL is null.
    if($url === null){
        return ["URL is not provided!", false];
    }

    set_error_handler(
        function ($severity, $message, $file, $line) {
            throw new ErrorException($message, $severity, $severity, $file, $line);
        }
    );

    //Load the webpage as string using PHP file_get_contents function. Return false if the host couldn't be resolved.
    try {
        $webpage_string = file_get_contents($url);
    }
    catch (Exception $e) {
        return [$e->getMessage(), false];
    }

    restore_error_handler();

    if($webpage_string !== false){
        //Creating an instance of PHP parser
        $dom = new Dom;

        //Loading the webpage content as string to the dom parser object.
        $dom->loadStr($webpage_string);

        //An empty array to store data.
        $data = [];

        //Finding all elements with class "package"
        $packages = $dom->find('.package');

        foreach ($packages as $key => $package){
            //Another Dom object to parse the packages result.
            $products = new Dom;
            $products->loadStr($package->innerHtml);

            $data[$key]['option title'] = $products->find('.header h3')->innerHtml();
            $data[$key]['option name'] = $products->find('.package-features ul li .package-name')->innerHtml();
            $data[$key]['description'] = $products->find('.package-features ul li .package-description')->innerHtml();

            //Initially storing the price for both Annual and Monthly price.
            $data[$key]['annual_price'] = $data[$key]['price'] = (float) str_replace('£', '', $products->find('.package-features ul li .package-price .price-big')->innerHtml());

            //checking if the .package-price element has string "Per Month"
            if(strpos($products->find('.package-features ul li .package-price')->innerHtml(), 'Per Month') !== false){
                //Multiple price value by 12 to get annual price.
                $data[$key]['annual_price'] = $data[$key]['price'] * 12;

                //Append appropriate price text.
                $data[$key]['price'] = $data[$key]['price'] . ' (inc. VAT) Per Month';
            }else{
                //Append appropriate price text.
                $data[$key]['price'] = $data[$key]['price'] . ' (inc. VAT) Per Year';
            }

            //Check if the price also has a discount element
            if($products->find('.package-features ul li .package-price p')->count() >= 1){
                $data[$key]['discount'] = $products->find('.package-features ul li .package-price p')->innerHtml();
            }else{
                $data[$key]['discount'] = '';
            }
        }

        //Creating a temp array based on column annual_price from the $data array.
        $column = array_column($data, 'annual_price');

        //Sorting the multiple array based on $column array
        array_multisort($column, SORT_DESC, $data);
        return [json_encode($data), true];
    }else{
        return ["Error reading the webpage", false];
    }

}