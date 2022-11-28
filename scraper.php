<?php

require('scrape.php');

//scrape('https://www.google.com/'); die();

//Check if argument count is not equal to 2 or argument is empty.
if($argc != 2 || $argv[1] == ''){
    echo "Please input the URL of the webpage to scrape data";
    echo "\n";
    echo "usage: php scraper.php https://www.your_url.com";
    return false;
}
else{
    //Call the scrape function using the URL from argument passed from the console.
    $result = scrape($argv[1]);
    echo $result[0];
}

