<?php

function get_two_random_images($dir) {

       $images = glob($dir . '/*.{jpg,jpeg,png,gif}', GLOB_BRACE);
       /*
       OLD WAY. ARRAY_RAND() DIDNT GIVE UNIFORMLY RANDOM RESULTS
       $random_indexA = array_rand($images);
       $random_imgA = $images[$random_indexA];
       unset($images[$random_indexA]); // remove image A to avoid duplicate images
       $random_imgB = $images[array_rand($images)];
       */
       shuffle($images);
       $random_imgA = $images[0];
       $random_imgB = $images[1];

       $two_images = array(); 
       array_push($two_images, $random_imgA, $random_imgB);

       return $two_images;
}