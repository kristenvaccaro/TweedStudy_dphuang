<?php
	function printTweets_SQL(){

	//SQL Authorization
	    $servername = "engr-cpanel-mysql.engr.illinois.edu";
	    $username = "twitterf_user";
	    $password = "IIA@kT$7maLt";
	    $dbname = "twitterf_tweet_store";

	    $db = new mysqli($servername, $username, $password, $dbname);

	    if($db->connect_errno > 0){
	        die('Unable to connect to database [' . $db->connect_error . ']');
	    }

			$user_id = $_SESSION["user_id"];

			var_dump($_SESSION['button']);

	//Retrieve session booleans
			$only_links_bool = $_SESSION['button']['only_links'];
			$no_links_bool = $_SESSION['button']['no_links'];
			$only_text_bool = $_SESSION['button']['only_text'];
			$no_text_bool = $_SESSION['button']['no_text'];
			$only_pics_bool = $_SESSION['button']['only_pics'];
			$no_pics_bool = $_SESSION['button']['no_pics'];
			$only_videos_bool = $_SESSION['button']['only_videos'];
			$no_videos_bool = $_SESSION['button']['no_videos'];
			$only_retweets_bool = $_SESSION['button']['only_retweets'];
			$no_retweets_bool = $_SESSION['button']['no_retweets'];
			$popular_bool = $_SESSION['button']['tweet_popular'];
			$unpopular_bool = $_SESSION['button']['tweet_unpopular'];
			$frequent_bool = $_SESSION['button']['poster_frequent'];
			$infrequent_bool = $_SESSION['button']['poster_infrequent'];
			$verified_bool = $_SESSION['button']['verified'];
			$unverified_bool = $_SESSION['button']['unverified'];
			$sentimentPos_bool = $_SESSION['button']['sentiment_positive'];
			$sentimentNeg_bool = $_SESSION['button']['sentiment_negative'];
			$closeFriends_bool = $_SESSION['button']['close_friends'];
			$distantFriends_bool = $_SESSION['button']['distant_friends'];
			$distanceSlider = $_SESSION['button']['distanceSlider'];
			$distanceSliderValue = $_SESSION['button']['distanceSliderValue'];
			$frequencySlider = $_SESSION['button']['frequencySlider'];
			$frequencySliderValue = $_SESSION['button']['frequencySliderValue'];
			$popularitySlider = $_SESSION['button']['popularitySlider'];
			$popularitySliderValue = $_SESSION['button']['popularitySliderValue'];
			$sessionArray = ['only_links', 'no_links', 'only_text', 'no_text','only_pics','no_pics', 'popularitySlider', 'popularitySliderValue',  'frequencySlider', 'frequencySliderValue', 'distanceSlider', 'distanceSliderValue', 'only_retweets', 'no_retweets', 'only_videos', 'no_videos', 'tweet_popular','tweet_unpopular','poster_frequent','poster_infrequent','verified','unverified','sentiment_positive','sentiment_negative','close_friends','distant_friends'];
			echo "<br>";
        foreach ($_SESSION['button'] as $key=>$val) {
            if (! in_array( $key, $sessionArray )) {
                $trend_bool = $val;
                $trend_name = $key;
								var_dump($trend_bool); echo "{$trend_name} <br>";
								if($trend_bool){
									echo "break";
									break;
								}
            }

        }

		echo $distanceSlider."<br>";
		echo $distanceSliderValue."<br>";



	    // (user_id, tweet_text, tweet_popularity, poster_frequency, verified, sentiment, user_url, user_profile_img_url, user_screen_name, tweet_create_date, tweet_urls, tweet_images, tweet_hashtags)


			// Execute two different base SQL syntaxes depending on if where care about closeness.

	//Create array of booleans and their corresponding statement
       $sql_filter_statements = array(
//									"closeFriends_bool" => array($closeFriends_bool, "LEFT JOIN `friends` ON `data`.`user_screen_name` = `friends`.`screen_name` WHERE `friends`.`user_id` = {$user_id} AND `friends`.`rank` > (SELECT max(`rank`) FROM `friends` WHERE `user_id` = {$user_id})/2 "),
//									"distantFriends_bool" => array($distantFriends_bool, "LEFT JOIN `friends` ON `data`.`user_screen_name` = `friends`.`screen_name` WHERE `friends`.`user_id` = {$user_id} AND `friends`.`rank` < (SELECT max(`rank`) FROM `friends` WHERE `user_id` = {$user_id})/2 "),
									"closeFriends_bool" => array($closeFriends_bool, "LEFT JOIN `friends` ON `data`.`user_screen_name` = `friends`.`screen_name` WHERE `friends`.`user_id` = {$user_id} AND `friends`.`computed_rank` > 0 "),
                  "distantFriends_bool" => array($distantFriends_bool, "LEFT JOIN `friends` ON `data`.`user_screen_name` = `friends`.`screen_name` WHERE `friends`.`user_id` = {$user_id} AND `friends`.`computed_rank` < 0 "),
									"only_links" => array($only_links_bool, "AND link = 1 "),
									"no_links" => array($no_links_bool, "AND link = 0 "),
									"only_retweets" => array($only_retweets_bool, "AND retweet = 1 "),
									"no_retweets" => array($no_retweets_bool, "AND retweet = 0 "),
									"only_text" => array($only_text_bool, "AND video = 0 AND picture = 0 "),
									"no_text" => array($no_text_bool, "AND tweet_text = '' "),
									"only_pics" => array($only_pics_bool, "AND picture = 1 "),
									"no_pics" => array($no_pics_bool, "AND picture = 0 "),
									"only_videos" => array($only_videos_bool, "AND video = 1 "),
									"no_videos" => array($no_videos_bool, "AND video = 0 "),
									"popular_bool" => array($popular_bool, "AND tweet_popularity > 10 "),
									"unpopular_bool" => array($unpopular_bool, "AND tweet_popularity < 10 "),
									"frequent_bool" => array($frequent_bool, "AND poster_frequency > 1000 "),
									"infrequent_bool" => array($infrequent_bool, "AND poster_frequency < 1000 "),
									"verified_bool" => array($verified_bool, "AND `data`.`verified` = 1 "),
									"unverified_bool" => array($unverified_bool, "AND `data`.`verified` = 0 "),
									"sentimentPos_bool" => array($sentimentPos_bool, "AND sentiment > 0 "),
									"sentimentNeg_bool" => array($sentimentNeg_bool, "AND sentiment < 0 "),
                  					"trend_bool" => array($trend_bool, "AND tweet_text LIKE  '%{$trend_name}%' "),
           );
		if($distanceSlider){
			if($distanceSliderValue > 0){
				$sql_filter_statements["closeFriends_bool"][1] = "LEFT JOIN `friends` ON `data`.`user_screen_name` = `friends`.`screen_name` WHERE `friends`.`user_id` = {$user_id} AND `friends`.`computed_rank` > ".$distanceSliderValue." ";
			}
			if($distanceSliderValue < 0){
				$sql_filter_statements["distantFriends_bool"][1] = "LEFT JOIN `friends` ON `data`.`user_screen_name` = `friends`.`screen_name` WHERE `friends`.`user_id` = {$user_id} AND `friends`.`computed_rank` < ".$distanceSliderValue." ";
			}
		}

		if($frequencySlider){
			if($frequencySliderValue > $_POST['middle']){
				$sql_filter_statements["frequent_bool"][1] = "AND poster_frequency > ".$frequencySliderValue." ";
			}elseif($frequencySliderValue < $_POST['middle']){
				$sql_filter_statements["infrequent_bool"][1] = "AND poster_frequency < ".$frequencySliderValue." ";
			}
		}

		if($popularitySlider){
			echo $popularitySliderValue.$_POST['middle']."<br>";
			if($popularitySliderValue > $_POST['middle']){
				$sql_filter_statements["popular_bool"][1] = "AND tweet_popularity > ".$popularitySliderValue." ";
			}elseif($popularitySliderValue < $_POST['middle']){
				$sql_filter_statements["unpopular_bool"][1] = "AND tweet_popularity < ".$popularitySliderValue." ";
			}
		}

	// Initalize filter statement
       $sql_filter = "";

    // Check each boolean then add statement if true
       foreach($sql_filter_statements as $statement){
           if ($statement[0]){
               $sql_filter .= $statement[1];
           }
       }

	    echo 'USERID IS ' . $user_id . "<br>";
	//Compose statement
		if($closeFriends_bool || $distantFriends_bool){
			$sql_syntax = "SELECT * FROM `data` ";
		}
		else{
			$sql_syntax = "SELECT * FROM `data` WHERE user_id = {$user_id} ";
		}

	    $sql = $sql_syntax . $sql_filter . "ORDER BY tweet_id DESC LIMIT 600";

		echo $sql;
	//Print each tweet
	    if(!$result = $db->query($sql)){
	        die('There was an error running the query [' . $db->error . ']');
	    }

	    while($row = $result->fetch_assoc()){
	        printEachTweet($row);
	    }

	    $db->close();
	}


    function printTweets_SQL_rand(){

        //SQL Authorization
        $servername = "engr-cpanel-mysql.engr.illinois.edu";
        $username = "twitterf_user";
        $password = "IIA@kT$7maLt";
        $dbname = "twitterf_tweet_store";

        $db = new mysqli($servername, $username, $password, $dbname);

        if($db->connect_errno > 0){
            die('Unable to connect to database [' . $db->connect_error . ']');
        }

        $user_id = $_SESSION["user_id"];

        //Retrieve session booleans
        $popular_bool = $_SESSION['button']['tweet_popular'];
        $unpopular_bool = $_SESSION['button']['tweet_unpopular'];
        $frequent_bool = $_SESSION['button']['poster_frequent'];
        $infrequent_bool = $_SESSION['button']['poster_infrequent'];
        $verified_bool = $_SESSION['button']['verified'];
        $unverified_bool = $_SESSION['button']['unverified'];
        $sentimentPos_bool = $_SESSION['button']['sentiment_positive'];
        $sentimentNeg_bool = $_SESSION['button']['sentiment_negative'];
        $closeFriends_bool = $_SESSION['button']['close_friends'];
        $distantFriends_bool = $_SESSION['button']['distant_friends'];
        $sessionArray = ['tweet_popular','tweet_unpopular','poster_frequent','poster_infrequent','verified','unverified','sentiment_positive','sentiment_negative','close_friends','distant_friends'];
        foreach ($_SESSION['button'] as $key=>$val) {
            if (! in_array( $key, $sessionArray )) {
                $trend_bool = $val;
                $trend_name = $key;
            }

        }


        // (user_id, tweet_text, tweet_popularity, poster_frequency, verified, sentiment, user_url, user_profile_img_url, user_screen_name, tweet_create_date, tweet_urls, tweet_images, tweet_hashtags)


        // Execute two different base SQL syntaxes depending on if where care about closeness.

        //Create array of booleans and their corresponding statement
        $sql_filter_statements = array(
                                       "closeFriends_bool" => array($closeFriends_bool, "LEFT JOIN `friends` ON `data`.`user_screen_name` = `friends`.`screen_name` WHERE `friends`.`user_id` = {$user_id} AND `friends`.`rank` > (SELECT max(`rank`) FROM `friends` WHERE `user_id` = {$user_id})/2 "),
                                       "distantFriends_bool" => array($distantFriends_bool, "LEFT JOIN `friends` ON `data`.`user_screen_name` = `friends`.`screen_name` WHERE `friends`.`user_id` = {$user_id} AND `friends`.`rank` < (SELECT max(`rank`) FROM `friends` WHERE `user_id` = {$user_id})/2 "),
                                       "popular_bool" => array($popular_bool, "AND tweet_popularity > 10 "),
                                       "unpopular_bool" => array($unpopular_bool, "AND tweet_popularity < 10 "),
                                       "frequent_bool" => array($frequent_bool, "AND poster_frequency > 1000 "),
                                       "infrequent_bool" => array($infrequent_bool, "AND poster_frequency < 1000 "),
                                       "verified_bool" => array($verified_bool, "AND `data`.`verified` = 1 "),
                                       "unverified_bool" => array($unverified_bool, "AND `data`.`verified` = 0 "),
                                       "sentimentPos_bool" => array($sentimentPos_bool, "AND sentiment > 0 "),
                                       "sentimentNeg_bool" => array($sentimentNeg_bool, "AND sentiment < 0 "),
                                       "trend_bool" => array($trend_bool, "AND tweet_text LIKE  '%{$trend_name}%' "),
                                       );
        // Initalize filter statement
        $sql_filter = "";

        // Check each boolean then add statement if true
        foreach($sql_filter_statements as $statement){
            if ($statement[0]){
                $sql_filter .= $statement[1];
            }
        }

        echo 'USERID IS ' . $user_id . "<br>";
        //Compose statement
        if($closeFriends_bool || $distantFriends_bool){
            $sql_syntax = "SELECT COUNT(*) FROM `data` ";
        }
        else{
            $sql_syntax = "SELECT COUNT(*) FROM `data` WHERE user_id = {$user_id} ";
        }

        $sql = $sql_syntax . $sql_filter . "ORDER BY tweet_create_date DESC LIMIT 600";

        echo $sql;
        //Print each tweet
        if(!$result = $db->query($sql)){
            die('There was an error running the query [' . $db->error . ']');
        }

        $numTweets = $result->fetch_assoc();
        echo "Number of tweets <br>";
        var_dump($numTweets["COUNT(*)"]);
        echo "<br>";

        //        $numTweets = (int)$numTweets;

        $sql_new = "SELECT * FROM (SELECT * FROM `data` WHERE user_id = {$user_id} ORDER BY RAND() LIMIT {$numTweets['COUNT(*)']}) a ORDER BY tweet_create_date DESC";

        echo $sql_new;

        if(!$result = $db->query($sql_new)){
            die('There was an error running the query [' . $db->error . ']');
        }

        while($row = $result->fetch_assoc()){
            printEachTweet($row);
        }

        $db->close();
    }

?>
