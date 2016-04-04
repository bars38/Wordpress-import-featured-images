<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
    </head>
    <body>
        <?php

        echo "Let's go!<br>";
//exit;
require  'lib/medoo.php';
$i = '0';

$database = new medoo([
	// required
	'database_type' => 'mysql',
	'database_name' => 'database',
	'server' => 'host',
	'username' => 'user_name',
	'password' => 'password',
	'charset' => 'utf8'

]);



//Select posts ID from old and new tables - wp_posts and xp_posts

$datas = $database->select("xp_posts", [

            "[>]wp_posts" => ["post_name" => "post_name"]
    ], [
            "xp_posts.id(id)",
            "xp_posts.post_title(post_title)",
            "xp_posts.post_name(post_name)",
            "wp_posts.id(idn)",
            "wp_posts.post_title(post_titlen)",
            "wp_posts.post_name(post_namen)"
    ], [
        "AND" => [
            "xp_posts.post_status" => 'publish',
            "xp_posts.post_parent" => '0',
            "xp_posts.post_type" => 'post'
            ]
]);

foreach($datas as $data)
{
	//echo "ID:" . $data["id"] . "IDnew:".$data["idn"]." - <br/>"; 
        $i++;
        if (!empty($data["idn"])){
            
// Search in xp_postmeta record with ID of Featured image

            $metas = $database->select("xp_postmeta", [
                "post_id",
                "meta_key",
                "meta_value"
            ], 
            ["AND" =>
                [
                "post_id" => $data["id"],
                "meta_key" => "_thumbnail_id"
                ]
            ]
            );   
                 
//echo $metas['0']["post_id"]."---".$data["idn"]."---".$metas['0']["meta_value"]."<br>";          
// Posts can be without featured image, so we make check          
if (!empty($metas['0']["meta_value"])){
    
// Select from old table all Featured Images information
    
            $images = $database->select("xp_posts", [
                "id",
                "post_title",
                "post_name",
                "post_mime_type",
                "guid"
            ], 
            ["AND" =>
                [
                    "post_status" => 'inherit',
                    "post_parent" => $data["id"],
                    "post_type" => 'attachment',
                    "id" => $metas['0']['meta_value']
                ]
            ],
            [
                    "ORDER" => "xp_posts.id DESC",
                    "LIMIT" => 1
            ]
            );
             
foreach($images as $img)
{
	//echo "ID:" . $img["id"] . "IDnew:".$img["guid"]." - <br/>";
    
    // Insert record with Images information to wp_post

        $last_user_id = $database->insert("wp_posts", [
            "post_author" => "1",
            "post_date" => "2015-12-15 11:13:52",
            "post_date_gmt" => "2015-12-15 08:13:52",
            "post_title" => $img["post_title"],
            "post_status" => "inherit",
            "comment_status" => "open",
            "ping_status" => "open",
            "post_name" => $img["post_name"],
            "post_modified" => "2016-01-25 01:03:55",
            "post_modified_gmt" => "2016-01-25 01:03:55",
            "post_parent" => $data["idn"],
            "guid" => $img["guid"],
            "menu_order" => "0",
            "post_type" => "attachment",
            "post_mime_type" => $img["post_mime_type"],
            "comment_count" => "0"
        ]); 
                
//////  select meta information from xp_postmeta
        
            $meta_all = $database->select("xp_postmeta", [
                "post_id",
                "meta_key",
                "meta_value"
            ], 
            [
                "post_id" => $metas['0']['meta_value']
            ]
            );        
//////  ###select from wp_postmeta /////     

foreach($meta_all as $meta)
    {
//echo "ID:" . $meta["post_id"] . "IDnew:".$meta["meta_value"]." - <br/>";

    // Insert meta information to wp_postmeta
            $last_id = $database->insert("wp_postmeta", [
                "post_id" => $last_user_id,
                "meta_key" => $meta["meta_key"],
                "meta_value" => $meta["meta_value"]
            ]); 
                    
    }
                
    // Insert meta information to wp_postmeta
    
    $last_id = $database->insert("wp_postmeta", [
        "post_id" => $data["idn"],
        "meta_key" => "_thumbnail_id",
        "meta_value" => $last_user_id
    ]);  
                            
                
} 

// Delete incorect records from wp_postmeta 
$meta_del = $database->select("wp_postmeta", [
        "meta_id"
    ], 
        ["AND" =>
            [
                "post_id" => $data["idn"],
                "meta_key" => "_thumbnail_id",
                "meta_value" => $metas['0']['meta_value']
            ]
    ]
    );  
if (!empty($meta_del['0']['meta_id'])){
    
$database->delete("wp_postmeta", [
	
		"meta_id" => $meta_del['0']['meta_id']

]);    
    
}

            
}            
        }
}

echo "<br><h1>All records - ".$i."</h1>"; 

        ?>
    </body>
</html>
