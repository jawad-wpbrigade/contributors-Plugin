# About Post-Authors
 
This Plugin will activate a custom meta box in the post/cpt editor.
It uses three Wordpress hooks: `add_meta_boxes`, `save_post`, `the_content` to achieve full functionality. 

## Backend

A custom query will be made using `WP_User_Query` to retrieve all the available authors and administrators.

At the end of the editor, all users will be listed using checkbox input.

![contributors-metabox-ineditor](https://user-images.githubusercontent.com/113098401/189866336-6a931c8b-b9e6-4f57-b15c-f9b4706ba85c.png)

The checkbox will be selected based on the previous selection. The author can make a new selection which will be reflected in the database.



After selecting from available users, the input values will be stored in  `contributors-array[]` array.

## Database

As mentioned above new input values will be stored in `contributors-array[]`. Using wordpress `save_post` hook and global `$_POST` variable the new values will
be stored in database using `update_post_meta`.

## Frontend
On individual post's the selected contributors will get rendered right after the end of post content. 

![contributors-on-frontend](https://user-images.githubusercontent.com/113098401/189868770-7f1803ec-6f74-4497-8219-068ac0431df8.png)

## short-code
Show list of contributors using shortcode `[contributors_shortcode]`.
this short code will only works on posts not on pages.

Reader can click on any contributor and will be redirected
to the linked author page on website.
