# Wordpress import featured images

This code will help you to import featured images to your new site.

### Steps

1. On old database make copy of tables **wp_posts** and **wp_postmeta** to **xp_posts** and **xp_postmeta** *(can do it with help PHPMyAdmin)*.
2. Export new tables **xp_posts** and **xp_postmeta** to .sql files.
3. Import these tables into new WordPress database.
4. Copy all images from **wp-content\uploads** on old site to new one.
5. In **index.php** write down options to access to WordPress database
```
$database = new medoo([
	// required
	'database_type' => 'mysql',
	'database_name' => 'database',
	'server' => 'host',
	'username' => 'user_name',
	'password' => 'password',
	'charset' => 'utf8'
]);
```
6. Check, do you copy **/lib/medoo.php**
7. Run **index.php**

## Don't forget to `make backup` of you current WordPress database before running this script!!!

If you have some questions or proposition, please write me an email: bars38@gmail.com

##### Thanks to [catfan](https://github.com/catfan) for [Medoo](https://github.com/catfan/Medoo)
