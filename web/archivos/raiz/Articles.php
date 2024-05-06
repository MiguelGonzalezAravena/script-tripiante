<?php
/*
SMF Articles
Version 1.1
by:vbgamer45
http://www.smfhacks.com
*/

if (!defined('SMF'))
  die('Hacking attempt...');

function ArticlesMain() {
  global $currentVersion, $modSettings, $boarddir, $boardurl;
  
  // Current version of the article system
  $currentVersion = '1.2';
  
  // Load the main Articles template
  loadtemplate('Articles');

  // Load the language files
  if (loadlanguage('Articles') == false)
    loadLanguage('Articles', 'english');

  // Check the articles path
  if (empty($modSettings['articlespath']))
    $modSettings['articles_path'] = $boarddir . '/articles/';

  if (empty($modSettings['articles_url']))
    $modSettings['articles_url'] = $boardurl . '/articles/';

  // Articles actions
  $subActions = array(
    'addarticle' => 'AddArticle',
    'addarticle2' => 'AddArticle2',
    'editarticle' => 'EditArticle',
    'editarticle2' => 'EditArticle2',
    'deletearticle' => 'DeleteArticle',
    'deletearticle2' => 'DeleteArticle2',
    'catup' => 'CatUpDown',
    'catdown' => 'CatUpDown',
    'addcat' => 'AddCat',
    'addcat2' => 'AddCat2',
    'editcat' => 'EditCat',
    'editcat2' => 'EditCat2',
    'deletecat' => 'DeleteCat',
    'deletecat2' => 'DeleteCat2',
    'approve' => 'Approve',
    'noapprove' => 'NoApprove',
    'alist' => 'ApproveList',
    'admin' => 'ArticlesAdmin',
    'admin2' => 'ArticlesAdmin2',
    'admincat' => 'ArticleAdminCats',
    'adminperm' => 'ArticlesAdminPerm',
    'catperm' => 'CatPerm',
    'catperm2' => 'CatPerm2',
    'catpermdelete' => 'CatPermDelete',
    'myarticles' => 'MyArticles',
    'search' => 'Search',
    'search2' => 'Search2',
    'view' => 'ViewArticle',
  );

  // Follow the sa or just go to main article index.
  if (isset($_REQUEST['sa']) && !empty($subActions[$_REQUEST['sa']]))
    $subActions[$_REQUEST['sa']]();
  else
    MainView();
}

function MainView() {
  global $context, $txt, $db_prefix, $modSettings, $ID_MEMBER, $scripturl, $user_info;

  // Check if the current user can view the articles list
  isAllowedTo('view_articles');

  // Load the main index articles template
  $context['sub_template']  = 'articlesmain';

  // To get Permissions text
  loadLanguage('Admin');
  
  $m_cats = allowedTo('articles_admin');
  $context['m_cats'] = $m_cats;
  $context['articles_cat_id'] = 0;
  $addarticle = allowedTo('add_articles');
  $context['addarticle'] = $addarticle;

  // MyArticles
  if ($addarticle && !($context['user']['is_guest'])) {
    $context['articles']['buttons']['mylisting'] =  array(
      'text' => 'smfarticles_myarticles',
      'url' =>$scripturl . '?action=articles;sa=myarticles;u=' . $ID_MEMBER,
      'lang' => true,
    );
  }

  // Search
  $context['articles']['buttons']['search'] =  array(
    'text' => 'smfarticles_search',
    'url' => $scripturl . '?action=articles;sa=search',
    'lang' => true,
  );

  // Check if there was a category
  if (isset($_REQUEST['cat'])) {
    $cat = htmlentities(addslashes($_REQUEST['cat']), ENT_QUOTES, 'UTF-8');

    // Check category level permission to view
    GetCatPermission($cat, 'view');

    $dbresult = db_query("
      SELECT title
      FROM {$db_prefix}articles_cat
      WHERE seotitle = '$cat' LIMIT 1", __FILE__, __LINE__);

    $row = mysqli_fetch_assoc($dbresult);

    // Set the page title
    $context['page_title'] = 'Ayuda';

    mysqli_free_result($dbresult);

    $context['articles_cat_id'] = $cat;
    $context['articles_cat_title'] = htmlentities($row['title'], ENT_QUOTES, 'ISO-8859-1');

    // Load the sub template
    $context['sub_template']  = 'articlelisting';

    if (isset($_REQUEST['sort']))
      $context['articles_sort'] = $_REQUEST['sort'];
    else
      $context['articles_sort'] = '';

    if (isset($_REQUEST['sorto']))
      $context['articles_sorto'] = $_REQUEST['sorto'];
    else
      $context['articles_sorto'] = '';

    if (isset($_REQUEST['sort'])) {
      switch(($_REQUEST['sort'])) {
        case 'title':
          $sort = 'a.title';
        break;
        case 'date':
          $sort = 'a.date';
        break;
        case 'rating':
          $sort = 'a.rating';
        break;
        case 'views':
          $sort = 'a.views';
        break;
        case 'username':
          $sort = 'm.realName';
        break;
        default:
          $sort = 'a.ID_ARTICLE';
      }
    }
    else
      $sort = 'a.ID_ARTICLE';

    if (isset($_REQUEST['sorto'])) {
      if ($_REQUEST['sorto'] == 'ASC')
        $sorto = 'ASC';
      else
        $sorto = 'DESC';
    }
    else
      $sorto = 'DESC';

    // Change sort order for articles
    if ($sorto == 'DESC')
      $newsorto = 'ASC';
    else
      $newsorto = 'DESC';

    $context['articles_newsorto'] = $newsorto;

    if (empty($modSettings['smfarticles_setarticlesperpage']))
      $modSettings['smfarticles_setarticlesperpage'] = 10;

    $context['start'] = (int) $_REQUEST['start'];

    // Show the articles in that category
    $dbresult = db_query("
      SELECT a.ID_ARTICLE, a.title, a.date, a.totalratings, m.realName, a.ID_MEMBER, a.description, a.views, a.ID_CAT, c.seotitle, c.ID_CAT, m.ID_MEMBER
      FROM ({$db_prefix}articles AS a, {$db_prefix}articles_cat AS c, {$db_prefix}members AS m)
      WHERE a.ID_CAT = c.ID_CAT
      AND a.ID_MEMBER = m.ID_MEMBER
      AND c.seotitle = '$cat'
      ORDER BY $sort $sorto
      LIMIT $context[start], " . $modSettings['smfarticles_setarticlesperpage'], __FILE__, __LINE__);

    $context['articles_listing'] = array();

    while ($row = mysqli_fetch_assoc($dbresult))
      $context['articles_listing'][] = $row;

    mysqli_free_result($dbresult);

    // Setup the paging
    $context['page_index'] = constructPageIndex($scripturl . '?action=articles;cat=' . $cat . ';sort=' . $context['articles_sort'] . ';sorto=' . $context['articles_sorto'], $_REQUEST['start'], GetTotalByCATID($cat), $modSettings['smfarticles_setarticlesperpage']);

    // Setup the Link Tree
    $context['linktree'][] = array(
      'url' => $scripturl . '?action=articles',
      'name' => $txt['smfarticles_title']
    );

    GetParentLink($cat);
  } else {
    if ($context['user']['is_guest'])
      $groupid = -1;
    else
      $groupid =  $user_info['groups'][0];

    // Set page title
    $context['page_title'] = 'Ayuda';

    $dbresult = db_query("
      SELECT c.ID_CAT, c.title, c.imageurl, c.filename, c.roworder, c.description, p.view, c.seotitle
      FROM {$db_prefix}articles_cat AS c
      LEFT JOIN {$db_prefix}articles_catperm AS p ON (p.ID_GROUP = $groupid AND c.ID_CAT = p.ID_CAT)
      WHERE c.ID_PARENT = 0 ORDER BY roworder ASC", __FILE__, __LINE__);

    $context['articles_cat'] = array();

    while ($row = mysqli_fetch_assoc($dbresult)) {
      // Skip category if they do not have permission to view it
      if ($row['view'] == '0')
        continue;

      $context['articles_cat'][] = $row;
    }

    mysqli_free_result($dbresult);

    // Only Admins need these queries to run
    if ($m_cats == true) {
      // Number of articles waiting approval
      $dbresult = db_query("
        SELECT COUNT(*) AS total
        FROM {$db_prefix}articles 
        WHERE approved = 0", __FILE__, __LINE__);

      $row = mysqli_fetch_assoc($dbresult);
      $context['articlesapproval'] = $row['total'];

      mysqli_free_result($dbresult);
    }
  }
}

function AddCat() {
  global $context, $txt, $db_prefix, $sourcedir, $modSettings;
  // Check if they are allowed to add a category
  isAllowedTo('articles_admin');

  $context['sub_template']  = 'addcat';

   // Set the page title
  $context['page_title'] = $txt['smfarticles_title'] . ' - ' . $txt['smfarticles_addcat'];

  $dbresult = db_query("
    SELECT ID_CAT, title, roworder, ID_PARENT
    FROM {$db_prefix}articles_cat
    ORDER BY title ASC", __FILE__, __LINE__);

  $context['articles_cat'] = array();

  while ($row = mysqli_fetch_assoc($dbresult)) {
    $context['articles_cat'][] = $row;
  }

  mysqli_free_result($dbresult);
  CreateArticlesPrettryCategory();

  if (isset($_REQUEST['cat']))
    $context['articles_parent'] = (int) $_REQUEST['cat'];
  else
    $context['articles_parent'] = 0;

  // Check if spellchecking is both enabled and actually working.
  $context['show_spellchecking'] = !empty($modSettings['enableSpellChecking']) && function_exists('pspell_new');

  /// Used for the editor
  require_once($sourcedir . '/Subs-Post.php');
  $context['post_box_name'] = 'description';
  $context['post_form'] = 'catform';
}

function AddCat2() {
  global $db_prefix, $txt;

  isAllowedTo('articles_admin');

  // Clean the input
  $title = htmlspecialchars($_REQUEST['title'], ENT_QUOTES);
  $description = htmlspecialchars($_REQUEST['description'], ENT_QUOTES);
  $image = htmlspecialchars($_REQUEST['image'], ENT_QUOTES);
  $parent = (int) $_REQUEST['parent'];

  if ($title == '')
    fatal_error($txt['smfarticles_nocattitle'], false);

  // Do the order
  $dbresult = db_query("
    SELECT roworder
    FROM {$db_prefix}articles_cat
    ORDER BY roworder DESC", __FILE__, __LINE__);

  $row = mysqli_fetch_assoc($dbresult);

  mysqli_free_result($dbresult);

  $order = $row['roworder'];
  $order++;

  // Insert the category
  db_query("
    INSERT INTO {$db_prefix}articles_cat(title, description, roworder, imageurl, ID_PARENT)
    VALUES ('$title', '$description', $order, '$image', $parent)", __FILE__, __LINE__);

  // Redirect back to the articles page
  redirectexit('action=articles');
}

function EditCat() {
  global $context, $txt, $db_prefix, $sourcedir, $modSettings;

  // Check if they can add categories
  isAllowedTo('articles_admin');

  $cat = (int) $_REQUEST['cat'];
  // Check if an article was selected
  if (empty($cat))
    fatal_error($txt['smfarticles_nocatselected']);

  // Load the subtemplate for the category
  $context['sub_template']  = 'editcat';

  // Set the page title
  $context['page_title'] = '' . $txt['smfarticles_editcat'];

  $dbresult = db_query("
    SELECT ID_CAT, title, roworder, ID_PARENT
    FROM {$db_prefix}articles_cat ORDER BY title ASC", __FILE__, __LINE__);

  $context['articles_cat'] = array();

  while ($row = mysqli_fetch_assoc($dbresult)) {
    // Can't be a parent of itself
    if ($row['ID_CAT'] == $cat)
      continue;

    $context['articles_cat'][] = $row;
  }

  mysqli_free_result($dbresult);
  CreateArticlesPrettryCategory();

  // Get category information
  $dbresult = db_query("
    SELECT ID_CAT, title, description, imageurl, ID_PARENT 
    FROM {$db_prefix}articles_cat 
    WHERE ID_CAT = $cat", __FILE__, __LINE__);

  $row = mysqli_fetch_assoc($dbresult);

  mysqli_free_result($dbresult);

  $context['articles_data'] = $row;

  // Check if spellchecking is both enabled and actually working.
  $context['show_spellchecking'] = !empty($modSettings['enableSpellChecking']) && function_exists('pspell_new');

  /// Used for the editor
  require_once($sourcedir . '/Subs-Post.php');
  $context['post_box_name'] = 'description';
  $context['post_form'] = 'catform';
}

function EditCat2() {
  global $db_prefix, $txt;

  isAllowedTo('articles_admin');

  // Clean the input
  $title = htmlspecialchars($_POST['title'], ENT_QUOTES);
  $description = htmlspecialchars($_POST['description'], ENT_QUOTES);
  $image = htmlspecialchars($_POST['image'], ENT_QUOTES);
  $catid = (int) $_REQUEST['catid'];
  $parent = (int) $_REQUEST['parent'];
  
  // Check if a category title was entered.
  if ($title == '')
    fatal_error($txt['smfarticles_nocattitle'], false);

  // Update the category
  db_query("
    UPDATE {$db_prefix}articles_cat
    SET title = '$title', ID_PARENT = $parent, description = '$description', imageurl = '$image'
    WHERE ID_CAT = $catid
    LIMIT 1", __FILE__, __LINE__);

  // Redirect back to the articles index
  redirectexit('action=articles');
}

function DeleteCat() {
  global $context, $mbname, $txt;

  isAllowedTo('articles_admin');

  // Get category id
  $catid = (int) $_REQUEST['cat'];

  if (empty($catid))
    fatal_error($txt['smfarticles_nocatselected']);

  $context['arcticle_cat'] = $catid;
  $context['sub_template']  = 'deletecat';

  // Set the page title
  $context['page_title'] = $mbname . ' - ' . $txt['smfarticles_title'] . ' - ' . $txt['smfarticles_deltcat'];
}

function DeleteCat2() {
  global $db_prefix;

  isAllowedTo('articles_admin');

  $catid = (int) $_REQUEST['catid'];

  $dbresult = db_query("
    SELECT ID_ARTICLE 
    FROM {$db_prefix}articles
    WHERE ID_CAT = $catid", __FILE__, __LINE__);

  // Update Category parent
  db_query("UPDATE {$db_prefix}articles_cat SET ID_PARENT = 0 WHERE ID_PARENT = $catid", __FILE__, __LINE__);

  // Delete All articles
  db_query("DELETE FROM {$db_prefix}articles WHERE ID_CAT = $catid", __FILE__, __LINE__);

  // Finally delete the category
  db_query("DELETE FROM {$db_prefix}articles_cat WHERE ID_CAT = $catid LIMIT 1", __FILE__, __LINE__);

  redirectexit('action=articles');
}

function AddArticle() {
  global $context, $txt, $db_prefix, $user_info, $modSettings, $sourcedir;

  isAllowedTo('add_articles');

  require_once($sourcedir . '/Subs-Post.php');

  $context['post_form'] = 'addarticle';
  $context['sub_template']  = 'addarticle';

  // Set the page title
  $context['page_title'] = '' . $txt['smfarticles_addarticle'];

  if ($context['user']['is_guest'])
    $groupid = -1;
  else
    $groupid =  $user_info['groups'][0];

  if (isset($_REQUEST['cat'])) {
    GetCatPermission($_REQUEST['cat'], 'addarticle');

    $catid = (int) $_REQUEST['cat'];
  }
  else
    $catid = 0;

  $context['articles_catid'] = $catid;

  $dbresult = db_query("
    SELECT c.ID_CAT, c.title, c.roworder, p.view, p.addarticle, c.ID_PARENT
    FROM {$db_prefix}articles_cat AS c
    LEFT JOIN {$db_prefix}articles_catperm AS p ON (p.ID_GROUP = $groupid AND c.ID_CAT = p.ID_CAT)
    ORDER BY c.title ASC", __FILE__, __LINE__);

  // Get category count
  $cat_count = db_affected_rows();
 
  if ($cat_count == 0)
    fatal_error($txt['smfarticles_nofirstcat']);

  $context['articles_cat'] = array();

  while ($row = mysqli_fetch_assoc($dbresult)) {
    // Check if they have permission to add to this category.
    if ($row['view'] == '0' || $row['addarticle'] == '0' )
      continue;

    $context['articles_cat'][] = $row;
  }

  mysqli_free_result($dbresult);
  CreateArticlesPrettryCategory();

  // Check if spellchecking is both enabled and actually working.
  $context['show_spellchecking'] = !empty($modSettings['enableSpellChecking']) && function_exists('pspell_new');
}

function AddArticle2() {
  global $ID_MEMBER, $db_prefix, $txt, $sourcedir, $modSettings;

  isAllowedTo('add_articles');

  // Clean the input
  $title = htmlspecialchars($_REQUEST['title'], ENT_QUOTES);
  $description = htmlspecialchars($_REQUEST['description'], ENT_QUOTES);
  $message = htmlspecialchars($_REQUEST['message'], ENT_QUOTES);
  $catid = (int) $_REQUEST['catid'];

  GetCatPermission($catid, 'addarticle');

  if (trim($title) == '')
    fatal_error($txt['smfarticles_noarticletitle'], false);

  if (trim($message) == '')
    fatal_error($txt['smfarticles_noarticletext'], false);

  // SEO Link not used
  $seotitle = str_replace(' ', '_', $title);

  // Get the current date/time
  $t = time();

  // Check if aarticle is already approved
  $approved = allowedTo('articles_auto_approve') ? 1 : 0;

  // Check if there is an image attachements
  $image_upload = false;

  if ($modSettings['smfarticles_allow_attached_images'] > 0 && isset($_FILES['uploadimage']['name']) && $_FILES['uploadimage']['name'] != '') {
    $sizes = @getimagesize($_FILES['uploadimage']['tmp_name']);

    // No size, then it's probably not a valid pic.
    if ($sizes === false)
      fatal_error($txt['smfarticles_error_invalid_picture'], false);

    $filesize = $_FILES['uploadimage']['size'];

    // Check FileSize
    if (!empty($modSettings['smfarticles_max_filesize']) && $filesize > $modSettings['smfarticles_max_filesize']) {
      // Delete the temp file
      @unlink($_FILES['uploadimage']['tmp_name']);
      fatal_error($txt['smfarticles_error_img_filesize'] . round($modSettings['smfarticles_max_filesize'] / 1024, 2) . 'kb', false);
    }

    $extensions = array(
      1 => 'gif',
      2 => 'jpeg',
      3 => 'png',
      5 => 'psd',
      6 => 'bmp',
      7 => 'tiff',
      8 => 'tiff',
      9 => 'jpeg',
      14 => 'iff',
    );

    $extension = isset($extensions[$sizes[2]]) ? $extensions[$sizes[2]] : '.bmp';

    // Copy the image
    $filename = $ID_MEMBER . '_' . date('d_m_y_g_i_s') . '.' . $extension;

    move_uploaded_file($_FILES['uploadimage']['tmp_name'], $modSettings['articles_path'] .  $filename);
    @chmod($modSettings['articles_path'] .  $filename, 0644);

    require_once($sourcedir . '/Subs-Graphics.php');

    createThumbnail($modSettings['articles_path'] .   $filename, 100, 100);
    rename($modSettings['articles_path'] .  $filename . '_thumb',  $modSettings['articles_path'] .  'thumb_' . $filename);

    $thumbname = 'thumb_' . $filename;

    @chmod($modSettings['articles_path'] .   'thumb_' . $filename, 0755);

    $image_upload  = true;
  }

  db_query("
    INSERT INTO {$db_prefix}articles (ID_CAT, title, description, ID_MEMBER, date, approved, seotitle)
    VALUES ($catid, '$title' , '$description', $ID_MEMBER, $t, $approved, '$seotitle')", __FILE__, __LINE__);

  $articleid = db_insert_id();

  // Insert the Page
  db_query("
    INSERT INTO {$db_prefix}articles_page (ID_ARTICLE, pagetext)
    VALUES ($articleid, '$message')", __FILE__, __LINE__);

  UpdateCategoryTotals($catid);

  // Redirect
  if ($approved)
    redirectexit('action=articles;cat=' . $catid);
  else 
    fatal_error($txt['smfarticles_articleneedsapproval']);
}

function EditArticle() {
  global $context, $mbname, $txt, $db_prefix, $modSettings, $sourcedir, $user_info, $ID_MEMBER;

  isAllowedTo('edit_articles');

  require_once($sourcedir . '/Subs-Post.php');

  $context['sub_template']  = 'editarticle';
  $context['post_form'] = 'editarticle';

  // Set the page title
  $context['page_title'] = $mbname  . ' - ' . $txt['smfarticles_title'] . ' - ' . $txt['smfarticles_editarticle'];

  if ($context['user']['is_guest'])
    $groupid = -1;
  else
    $groupid =  $user_info['groups'][0];

  // Lookup the article and see if they can edit it.
  $id = (int) $_REQUEST['id'];

  if (empty($id))
    fatal_error($txt['smfarticles_noarticleselected']);

  $dbresult = db_query("
    SELECT ID_ARTICLE,ID_CAT,ID_MEMBER 
    FROM {$db_prefix}articles 
    WHERE ID_ARTICLE = $id LIMIT 1", __FILE__, __LINE__);

  $row = mysqli_fetch_assoc($dbresult);

  mysqli_free_result($dbresult);  
  GetCatPermission($row['ID_CAT'], 'editarticle');

  $m_cats = allowedTo('articles_admin');
  $edit_article = allowedTo('edit_articles');

  // Check if the article belongs to them
  if ($m_cats == FALSE && ($edit_article == false || $row['ID_MEMBER'] != $ID_MEMBER))
    fatal_error($txt['smfarticles_err_articleedit'], false);
  // End security check

  $context['article_id'] = $id;

  $dbresult = db_query("
    SELECT ID_ARTICLE, title, ID_CAT, description
    FROM {$db_prefix}articles
    WHERE ID_ARTICLE = $id LIMIT 1", __FILE__, __LINE__);

  $row = mysqli_fetch_assoc($dbresult);

  mysqli_free_result($dbresult);

  $context['article_data'] = $row;

  // Get Article Page Data
  $dbresult = db_query("
    SELECT pagetext FROM {$db_prefix}articles_page
    WHERE ID_ARTICLE = $id LIMIT 1", __FILE__, __LINE__);

  $row = mysqli_fetch_assoc($dbresult);
  $context['article_page'] = $row;

  mysqli_free_result($dbresult);

  $dbresult = db_query("
    SELECT c.ID_CAT, c.title, c.roworder, p.view, p.addarticle, c.ID_PARENT
    FROM {$db_prefix}articles_cat AS c
    LEFT JOIN {$db_prefix}articles_catperm AS p ON (p.ID_GROUP = $groupid AND c.ID_CAT = p.ID_CAT)
    ORDER BY c.title ASC", __FILE__, __LINE__);

  // Get category count
  $cat_count = db_affected_rows();

  if ($cat_count == 0)
    fatal_error($txt['smfarticles_nofirstcat']);

  $context['articles_cat'] = array();

  while ($row = mysqli_fetch_assoc($dbresult)) {
    // Check if they have permission to add to this category.
    if ($row['view'] == '0' || $row['addarticle'] == '0')
      continue;

    $context['articles_cat'][] =  $row;
  }

  mysqli_free_result($dbresult);
  CreateArticlesPrettryCategory();

  // Check if spellchecking is both enabled and actually working.
  $context['show_spellchecking'] = !empty($modSettings['enableSpellChecking']) && function_exists('pspell_new');
}

function EditArticle2() {
  global $db_prefix, $txt, $ID_MEMBER;

  isAllowedTo('edit_articles');

  $id = (int) $_REQUEST['id'];

  if (empty($id))
    fatal_error($txt['smfarticles_noarticleselected']);

  // Clean the input
  $title = htmlspecialchars($_REQUEST['title'], ENT_QUOTES);
  $description = htmlspecialchars($_REQUEST['description'], ENT_QUOTES);
  $message = htmlspecialchars($_REQUEST['message'], ENT_QUOTES);
  $catid = (int) $_REQUEST['catid'];

  // Check category level permission
  GetCatPermission($catid, 'editarticle');

  // Check if the article belongs to them
  $dbresult = db_query("
    SELECT ID_ARTICLE, ID_CAT, ID_MEMBER
    FROM {$db_prefix}articles
    WHERE ID_ARTICLE = $id LIMIT 1", __FILE__, __LINE__);

  $row = mysqli_fetch_assoc($dbresult);

  mysqli_free_result($dbresult);

  $m_cats = allowedTo('articles_admin');
  $edit_article = allowedTo('edit_articles');

  // Check if the article belongs to them
  if ($m_cats == FALSE && ($edit_article == false || $row['ID_MEMBER'] != $ID_MEMBER))
    fatal_error($txt['smfarticles_err_articleedit'], false);

  if (trim($title) == '')
    fatal_error($txt['smfarticles_noarticletitle'], false);

  if (trim($message) == '')
    fatal_error($txt['smfarticles_noarticletext'], false);

  // Update the article
  db_query("
    UPDATE {$db_prefix}articles
    SET title = '$title', description = '$description', ID_CAT = $catid
    WHERE ID_ARTICLE = $id LIMIT 1", __FILE__, __LINE__);

  // Update cateogry totals
  if ($row['ID_CAT'] != $catid) {
    UpdateCategoryTotals($catid);
    UpdateCategoryTotals($row['ID_CAT']);
  }

  // Update the article page
  db_query("
    UPDATE {$db_prefix}articles_page
    SET pagetext = '$message'
    WHERE ID_ARTICLE = $id
    LIMIT 1", __FILE__, __LINE__);

  // Redirect back to article
  redirectexit('action=articles;sa=view;article=' . $id);
}

function DeleteArticle() {
  global $context, $mbname, $txt, $db_prefix, $ID_MEMBER;

  isAllowedTo('delete_articles');

  $id = (int) $_REQUEST['id'];

  if (empty($id))
    fatal_error($txt['smfarticles_noarticleselected']);

  // Check if they are allowed to delete the articles
  $dbresult = db_query("
    SELECT ID_ARTICLE, ID_CAT, ID_MEMBER, title
    FROM {$db_prefix}articles
    WHERE ID_ARTICLE = $id LIMIT 1", __FILE__, __LINE__);

  $row = mysqli_fetch_assoc($dbresult);

  mysqli_free_result($dbresult);
  GetCatPermission($row['ID_CAT'], 'delarticle');

  // Check if they own this article
  $m_cats = allowedTo('articles_admin');
  $delete_article = allowedTo('delete_articles');

  // Check if the article belongs to them
  if ($m_cats == FALSE && ($delete_article == false || $row['ID_MEMBER'] != $ID_MEMBER))
    fatal_error($txt['smfarticles_err_articledelete'], false);

  $context['sub_template']  = 'deletearticle';
  $context['article_id'] = $id;
  $context['article_title'] = htmlentities($row['title'], ENT_QUOTES, 'ISO-8859-1');

  // Set the page title
  $context['page_title'] = $mbname  . ' - ' . $txt['smfarticles_title'] . ' - ' . $txt['smfarticles_delarticle'];
}

function DeleteArticle2() {
  global $db_prefix, $txt, $ID_MEMBER;

  isAllowedTo('delete_articles');

  $id = (int) $_REQUEST['id'];

  if (empty($id))
    fatal_error($txt['smfarticles_noarticleselected']);

  // Check if they are allowed to delete the articles
  $dbresult = db_query("
    SELECT ID_ARTICLE, ID_CAT, ID_MEMBER
    FROM {$db_prefix}articles
    WHERE ID_ARTICLE = $id LIMIT 1", __FILE__, __LINE__);

  $row = mysqli_fetch_assoc($dbresult);

  mysqli_free_result($dbresult);
  GetCatPermission($row['ID_CAT'], 'delarticle');

  // Check if they own this article
  $m_cats = allowedTo('articles_admin');
  $delete_article = allowedTo('delete_articles');

  // Check if the article belongs to them
  if ($m_cats == FALSE && ($delete_article == false || $row['ID_MEMBER'] != $ID_MEMBER))
    fatal_error($txt['smfarticles_err_articledelete'], false);

  // Delete the Article
  db_query("
    DELETE FROM {$db_prefix}articles
    WHERE ID_ARTICLE = $id LIMIT 1", __FILE__, __LINE__);

  // Delete pages
  db_query("
    DELETE FROM {$db_prefix}articles_page
    WHERE ID_ARTICLE = $id LIMIT 1", __FILE__, __LINE__);

  // Update the totals for that category
  UpdateCategoryTotals($row['ID_CAT']);
  redirectexit('action=articles;cat=' . $row['ID_CAT']);
}

function CatUpDown() {
  global $db_prefix, $txt;

  // Check if they are allowed to manage cats
  isAllowedTo('articles_admin');

  // Get the cat id
  $cat = (int) $_REQUEST['cat'];

  ReOrderCats($cat);

  // First get our row order
  $dbresult1 = db_query("
    SELECT roworder
    FROM {$db_prefix}articles_cat
    WHERE ID_CAT = $cat", __FILE__, __LINE__);

  $row = mysqli_fetch_assoc($dbresult1);
  $oldrow = $row['roworder'];
  $o = $row['roworder'];

  if ($_GET['sa'] == 'catup')
    $o--;
  else
    $o++;

  mysqli_free_result($dbresult1);

  $dbresult = db_query("
    SELECT ID_CAT, roworder
    FROM {$db_prefix}articles_cat
    WHERE roworder = $o", __FILE__, __LINE__);

  if (db_affected_rows()== 0) {
    if ($_GET['sa'] == 'catup')
      fatal_error($txt['smfarticles_nocatabove'], false);
    else
      fatal_error($txt['smfarticles_nocatbelow'], false);
  }

  $row2 = mysqli_fetch_assoc($dbresult);

  mysqli_free_result($dbresult);

  // Swap the order Id's
  db_query("
    UPDATE {$db_prefix}articles_cat
    SET roworder = $oldrow
    WHERE ID_CAT = " . $row2['ID_CAT'], __FILE__, __LINE__);

  db_query("
    UPDATE {$db_prefix}articles_cat
    SET roworder = $o
    WHERE ID_CAT = $cat", __FILE__, __LINE__);

  // Redirect to index to view cats
  redirectexit('action=articles');
}

function ApproveList() {
  global $context, $mbname, $txt, $db_prefix, $scripturl;

  isAllowedTo('articles_admin');
  adminIndex('articles_settings');

  $context['editarticle'] = allowedTo('edit_articles');
  $context['deletearticle'] = allowedTo('delete_articles');
  $context['sub_template']  = 'approvearticles';

  DoArticleAdminTabs();

  // Get Total Pages
  $dbresult = db_query("
    SELECT COUNT(*) AS total
    FROM {$db_prefix}articles
    WHERE approved = 0", __FILE__, __LINE__);

  $row = mysqli_fetch_assoc($dbresult);
  $total = $row['total'];

  mysqli_free_result($dbresult);

  $context['start'] = (int) $_REQUEST['start'];

  $dbresult = db_query("
    SELECT
      l.ID_ARTICLE, l.approved,l.description, l.title,l.date, m.realName,
      l.ID_MEMBER, l.description,l.views, l.ID_CAT, c.title catname
    FROM ({$db_prefix}articles AS l, {$db_prefix}articles_cat AS c)
    LEFT JOIN {$db_prefix}members AS m ON (l.ID_MEMBER = m.ID_MEMBER)
    WHERE l.ID_CAT = c.ID_CAT AND l.approved = 0 ORDER BY l.ID_ARTICLE DESC
    LIMIT $context[start], 20", __FILE__, __LINE__);

  $context['articles_list'] = array();

  while ($row = mysqli_fetch_assoc($dbresult))
    $context['articles_list'][] = $row;

  mysqli_free_result($dbresult);

  $context['page_index'] = constructPageIndex($scripturl . '?action=articles;cat=' , $_REQUEST['start'], $total, 20);

  // Set the page title
  $context['page_title'] = $mbname . ' - ' . $txt['smfarticles_title'] .' - ' . $txt['smfarticles_approvearticles'];
}

function Approve() {
  global $db_prefix, $txt;

  isAllowedTo('articles_admin');

  // Get articles id
  $id = (int) $_REQUEST['id'];

  if (empty($id))
    fatal_error($txt['smfarticles_noarticleselected']);

  db_query("
    UPDATE {$db_prefix}articles
    SET approved = 1
    WHERE ID_ARTICLE = $id
    LIMIT 1", __FILE__, __LINE__);

  UpdateCategoryTotalByArticleID($id);
  redirectexit('action=articles;sa=alist');
}

function NoApprove() {
  global $db_prefix, $txt;

  isAllowedTo('articles_admin');

  // Get article id
  $id = (int) $_REQUEST['id'];

  if (empty($id))
    fatal_error($txt['smfarticles_noarticleselected']);

  db_query("
    UPDATE {$db_prefix}articles
    SET approved = 0
    WHERE ID_ARTICLE = $id
    LIMIT 1", __FILE__, __LINE__);

  UpdateCategoryTotalByArticleID($id);
  redirectexit('action=articles');
}

function ArticlesAdmin() {
  global $context, $mbname, $txt;

  isAllowedTo('articles_admin');

  $context['page_title'] = $mbname . ' - ' . $txt['smfarticles_title'] . ' - ' . $txt['smfarticles_articlesconfig'];

  adminIndex('articles_settings');
  DoArticleAdminTabs();

  $context['sub_template']  = 'settings';
}

function ArticlesAdmin2() {
  isAllowedTo('articles_admin');

  $smfarticles_setarticlesperpage = (int) $_REQUEST['smfarticles_setarticlesperpage'];
  $smfarticles_countsubcats = isset($_REQUEST['smfarticles_countsubcats']) ? 1 : 0;
  $smfarticles_disp_views = isset($_REQUEST['smfarticles_disp_views']) ? 1 : 0;
  $smfarticles_disp_membername = isset($_REQUEST['smfarticles_disp_membername']) ? 1 : 0;
  $smfarticles_disp_date = isset($_REQUEST['smfarticles_disp_date']) ? 1 : 0;

  // Save the setting information
  updateSettings(
    array(
    'smfarticles_setarticlesperpage' => $smfarticles_setarticlesperpage,
    'smfarticles_countsubcats' => $smfarticles_countsubcats,  
    'smfarticles_disp_views' => $smfarticles_disp_views,
    'smfarticles_disp_membername' => $smfarticles_disp_membername,
    'smfarticles_disp_date' => $smfarticles_disp_date,
    )
  );

  redirectexit('action=articles;sa=admin');
}

function ArticlesAdminCats() {
  global $context, $mbname, $txt;

  isAllowedTo('articles_admin');

  $context['page_title'] = $mbname . ' - ' . $txt['smfarticles_title'] . ' - ' . $txt['smfarticles_managecats'];

  adminIndex('articles_settings');

  $context['sub_template']  = 'manage_cats';
}

function ArticlesAdminPerm() {
  global $context, $mbname, $txt, $db_prefix;

  isAllowedTo('articles_admin');
  adminIndex('articles_settings');
  DoArticleAdminTabs();

  $context['page_title'] = $mbname . ' - ' . $txt['smfarticles_title'] . ' - ' . $txt['smfarticles_catpermlist'];
  $context['sub_template']  = 'catpermlist';

  $dbresult = db_query("
    SELECT c.ID_CAT, c.ID, c.view, c.addarticle, c.editarticle, c.delarticle, c.ID_GROUP, m.groupName, a.title catname
    FROM ({$db_prefix}articles_catperm as c, {$db_prefix}membergroups AS m,{$db_prefix}articles_cat as a)
    WHERE c.ID_GROUP = m.ID_GROUP AND a.ID_CAT = c.ID_CAT", __FILE__, __LINE__);

  $context['articles_mbgroups'] = array();

  while ($row = mysqli_fetch_assoc($dbresult))
    $context['articles_mbgroups'][] = $row;

  mysqli_free_result($dbresult);

  $dbresult = db_query("
    SELECT c.ID_CAT, c.ID, c.view, c.addarticle, c.editarticle, c.delarticle, c.ID_GROUP, a.title catname
    FROM ({$db_prefix}articles_catperm as c,{$db_prefix}articles_cat as a)
    WHERE  c.ID_GROUP = 0 AND a.ID_CAT = c.ID_CAT LIMIT 1", __FILE__, __LINE__);

  $context['articles_regular'] = array();

  while ($row = mysqli_fetch_assoc($dbresult))
    $context['articles_regular'][] = $row;

  mysqli_free_result($dbresult);

  $dbresult = db_query("
    SELECT c.ID_CAT, c.ID, c.view, c.addarticle, c.editarticle, c.delarticle, c.ID_GROUP, a.title catname
    FROM ({$db_prefix}articles_catperm as c,{$db_prefix}articles_cat as a)
    WHERE  c.ID_GROUP = -1 AND a.ID_CAT = c.ID_CAT LIMIT 1", __FILE__, __LINE__);

  $context['articles_guests'] = array();

  while ($row = mysqli_fetch_assoc($dbresult))
    $context['articles_guests'][] = $row;

  mysqli_free_result($dbresult);
}

function GetCatPermission($cat, $perm) {
  global $ID_MEMBER, $db_prefix, $txt, $user_info;

  $cat = (int) $cat;

  if (!$user_info['is_guest']) {
    $dbresult = db_query("
      SELECT m.ID_MEMBER, c.view, c.addarticle, c.editarticle, c.delarticle,c.ratearticle, c.report
      FROM ({$db_prefix}articles_catperm as c, {$db_prefix}members as m)
      WHERE m.ID_MEMBER = $ID_MEMBER AND c.ID_GROUP = m.ID_GROUP AND c.ID_CAT = $cat LIMIT 1", __FILE__, __LINE__);
  }
  else
    $dbresult = db_query("
      SELECT c.view, c.addarticle, c.editarticle, c.delarticle,c.ratearticle, c.report
      FROM {$db_prefix}articles_catperm as c
      WHERE c.ID_GROUP = -1 AND c.ID_CAT = $cat LIMIT 1", __FILE__, __LINE__);

  if (db_affected_rows() == 0)
    mysqli_free_result($dbresult);
  else {
    $row = mysqli_fetch_assoc($dbresult);
    mysqli_free_result($dbresult);
    
    if ($perm == 'view' && $row['view'] == 0)
      fatal_error($txt['smfarticles_perm_no_view'], false);
    else if ($perm == 'addarticle' && $row['addarticle'] == 0)
      fatal_error($txt['smfarticles_perm_no_add'], false);
    else if ($perm == 'editarticle' && $row['editarticle'] == 0)
      fatal_error($txt['smfarticles_perm_no_edit'], false);
    else if ($perm == 'delarticle' && $row['delarticle'] == 0)
      fatal_error($txt['smfarticles_perm_no_delete'], false);
    else if ($perm == 'ratelarticle' && $row['ratearticle'] == 0)
      fatal_error($txt['smfarticles_perm_no_ratelink'], false);
    else if ($perm == 'report' && $row['report'] == 0)
      fatal_error($txt['smfarticles_perm_no_report'], false);
  }
}

function CatPermDelete() {
  global $db_prefix;

  isAllowedTo('articles_admin');

  $id = (int) $_REQUEST['id'];

  // Delete the Permission
  db_query("
    DELETE FROM {$db_prefix}articles_catperm
    WHERE ID = " . $id . ' LIMIT 1', __FILE__, __LINE__);

  // Redirect to the permissions list
  redirectexit('action=articles;sa=adminperm');
}

function CatPerm() {
  global $mbname, $txt, $db_prefix, $context;

  isAllowedTo('articles_admin');

  $cat = (int) $_REQUEST['cat'];

  if (empty($cat))
    fatal_error($txt['smfarticles_nocatselected']);

  $dbresult1 = db_query("
    SELECT ID_CAT, title
    FROM {$db_prefix}articles_cat
    WHERE ID_CAT = $cat
    LIMIT 1", __FILE__, __LINE__);

  $row1 = mysqli_fetch_assoc($dbresult1);
  $context['articles_cat_name'] = $row1['title'];

  mysqli_free_result($dbresult1);
  loadLanguage('Admin');

  $context['articles_cat'] = $cat;

  // Load the template
  $context['sub_template']  = 'catperm';

  // Set the page title
  $context['page_title'] = $mbname . ' - ' . $txt['smfarticles_title'] . ' - ' . $txt['smfarticles_text_catperm'] . ' -' . $context['articles_cat_name'];

  // Load the membergroups
  $dbresult = db_query("
    SELECT ID_GROUP, groupName 
    FROM {$db_prefix}membergroups 
    WHERE minPosts = -1
    ORDER BY groupName", __FILE__, __LINE__);

  while ($row = mysqli_fetch_assoc($dbresult)) {
    $context['groups'][$row['ID_GROUP']] = array(
      'ID_GROUP' => $row['ID_GROUP'],
      'groupName' => $row['groupName'],
    );
  }

  mysqli_free_result($dbresult);

  // membergroups
  $dbresult = db_query("
    SELECT c.ID_CAT, c.ID, c.view, c.addarticle, c.editarticle, c.delarticle, c.ID_GROUP, m.groupName, a.title catname
    FROM ({$db_prefix}articles_catperm as c, {$db_prefix}membergroups AS m,{$db_prefix}articles_cat as a)
    WHERE  c.ID_CAT = " . $context['articles_cat'] . "
    AND c.ID_GROUP = m.ID_GROUP
    AND a.ID_CAT = c.ID_CAT", __FILE__, __LINE__);

  $context['articles_membergroup'] = array();

  while ($row = mysqli_fetch_assoc($dbresult))
    $context['articles_membergroup'][] = $row;

  mysqli_free_result($dbresult);

  $dbresult = db_query("
    SELECT c.ID_CAT, c.ID, c.view, c.addarticle, c.editarticle, c.delarticle, c.ID_GROUP, a.title catname
    FROM ({$db_prefix}articles_catperm as c,{$db_prefix}articles_cat as a)
    WHERE c.ID_CAT = " . $context['articles_cat'] . "
    AND c.ID_GROUP = 0
    AND a.ID_CAT = c.ID_CAT
    LIMIT 1", __FILE__, __LINE__);

  $context['articles_reggroup'] = array();

  while ($row = mysqli_fetch_assoc($dbresult))
    $context['articles_reggroup'][] = $row;

  mysqli_free_result($dbresult);

  // Guests
  $dbresult = db_query("
    SELECT c.ID_CAT, c.ID, c.view, c.addarticle, c.editarticle, c.delarticle, c.ID_GROUP, a.title catname
    FROM ({$db_prefix}articles_catperm as c,{$db_prefix}articles_cat as a)
    WHERE c.ID_CAT = " . $context['articles_cat'] . "
    AND c.ID_GROUP = -1
    AND a.ID_CAT = c.ID_CAT
    LIMIT 1", __FILE__, __LINE__);

  $context['articles_guest'] = array();

  while ($row = mysqli_fetch_assoc($dbresult))
    $context['articles_guest'][] = $row;

  mysqli_free_result($dbresult);
}

function CatPerm2() {
  global  $db_prefix, $txt;

  isAllowedTo('articles_admin');

  $groupname = (int) $_REQUEST['groupname'];
  $cat = (int) $_REQUEST['cat'];

  // Check if permission exits
  $dbresult = db_query("
    SELECT ID_GROUP, ID_CAT
    FROM {$db_prefix}articles_catperm
    WHERE ID_GROUP = $groupname
    AND ID_CAT = $cat", __FILE__, __LINE__);

  if (db_affected_rows()!= 0) {
    mysqli_free_result($dbresult);
    fatal_error($txt['smfarticles_permerr_permexist'], false);
  }

  mysqli_free_result($dbresult);

  // Permissions
  $view = isset($_REQUEST['view']) ? 1 : 0;
  $add = isset($_REQUEST['add']) ? 1 : 0;
  $edit = isset($_REQUEST['edit']) ? 1 : 0;
  $delete = isset($_REQUEST['delete']) ? 1 : 0;

  // Insert into database
  db_query("
    INSERT INTO {$db_prefix}articles_catperm(ID_GROUP, ID_CAT, view, addarticle, editarticle, delarticle)
    VALUES ($groupname, $cat, $view, $add, $edit, $delete)", __FILE__, __LINE__);

  redirectexit('action=articles;sa=catperm;cat=' . $cat);
}

function ReOrderCats($cat) {
  global $db_prefix;

  $dbresult1 = db_query("
    SELECT roworder, ID_PARENT
    FROM {$db_prefix}articles_cat
    WHERE ID_CAT = $cat", __FILE__, __LINE__);

  $row = mysqli_fetch_assoc($dbresult1);
  $ID_PARENT = $row['ID_PARENT'];

  mysqli_free_result($dbresult1);

  $dbresult = db_query("
    SELECT ID_CAT, roworder
    FROM {$db_prefix}articles_cat
    WHERE ID_PARENT = $ID_PARENT
    ORDER BY roworder ASC", __FILE__, __LINE__);

  if (db_affected_rows() != 0) {
    $count = 1;

    while ($row2 = mysqli_fetch_assoc($dbresult)) {
      db_query("
        UPDATE {$db_prefix}articles_cat
        SET roworder = $count
        WHERE ID_CAT = " . $row2['ID_CAT'], __FILE__, __LINE__);

      $count++;
    }
  }

  mysqli_free_result($dbresult);
}

function DoArticleAdminTabs($overrideSelected = '') {
  global $context, $txt, $scripturl, $db_prefix;

  $tmpSA = '';

  if (!empty($overrideSelected))
    $_REQUEST['sa'] = $overrideSelected;

  // Get the number articles waiting for approval
  $dbresult = db_query("
    SELECT COUNT(*) as total
    FROM {$db_prefix}articles
    WHERE approved = 0", __FILE__, __LINE__);

  $row = mysqli_fetch_assoc($dbresult);
  $articles_total = $row['total'];

  mysqli_free_result($dbresult);

  // Create the tabs for the template.
  $context['admin_tabs'] = array(
    'title' => $txt['smfarticles_admin'],
    'description' => '',
    'tabs' => array(),
  );

  $context['admin_tabs']['tabs'][] = array(
    'title' =>  $txt['smfarticles_articlessettings'],
    'description' => '',
    'href' => $scripturl . '?action=articles;sa=admin',
    'is_selected' => $_REQUEST['sa'] == 'admin',
  );

  $context['admin_tabs']['tabs'][] = array(
    'title' => $txt['smfarticles_approvearticles'] . ' (' . $articles_total . ')',
    'description' => '',
    'href' => $scripturl . '?action=articles;sa=alist',
    'is_selected' => $_REQUEST['sa'] == 'alist',
  );

  $context['admin_tabs']['tabs'][] = array(
    'title' => $txt['smfarticles_catpermlist'],
    'description' => '',
    'href' => $scripturl . '?action=articles;sa=adminperm',
    'is_selected' => $_REQUEST['sa'] == 'adminperm',
  );

  if (!empty($overrideSelected)) {
    $_REQUEST['sa'] = $tmpSA;
  }

  $context['admin_tabs']['tabs'][count($context['admin_tabs']['tabs']) - 1]['is_last'] = true;
}

function GetArticleTotals($cat) {
  global $modSettings, $db_prefix, $subcats_linktree, $scripturl;

  $total = 0;

  $total += GetTotalByCATID($cat);
  $subcats_linktree = '';

  // Get the child categories to this category
  if ($modSettings['smfarticles_countsubcats']) {
    $firstCatDone  = 0;

    $dbresult3 = db_query("
      SELECT ID_CAT, total, title, seotitle
      FROM {$db_prefix}articles_cat 
      WHERE seotitle = '$cat'
      ORDER BY roworder ASC", __FILE__, __LINE__);

    while ($row3 = mysqli_fetch_assoc($dbresult3)) {
      if ($firstCatDone == 1)
        $subcats_linktree .=',&nbsp;';

      $firstCatDone = 1;
      $subcats_linktree .= '<a href="' . $scripturl . '?action=articles;cat=' . $row3['ID_CAT'] . '">' . $row3['title'] . '</a>';

      if ($row3['total'] == -1) {
        $dbresult = db_query("
          SELECT COUNT(*) AS total 
          FROM {$db_prefix}articles AS a, {$db_prefix}articles_cat AS ac
          WHERE a.ID_CAT = ac.ID_CAT
          AND ac.seotitle = '$cat'
          AND ac.ID_CAT = " . $row3['ID_CAT'], __FILE__, __LINE__);

        $row = mysqli_fetch_assoc($dbresult);
        $total2 = $row['total'];

        mysqli_free_result($dbresult);

        $dbresult = db_query("
          UPDATE {$db_prefix}articles_cat
          SET total = $total2
          WHERE seotitle =  " . $row3['seotitle'] . "
          LIMIT 1", __FILE__, __LINE__);
      }
    }

    mysqli_free_result($dbresult3);

    $dbresult3 = db_query("
      SELECT SUM(total) AS finaltotal
      FROM {$db_prefix}articles_cat
      WHERE seotitle = '$cat'", __FILE__, __LINE__);

    $row3 = mysqli_fetch_assoc($dbresult3);

    mysqli_free_result($dbresult3);

    if ($row3['finaltotal'] != '')
      $total += $row3['finaltotal'];
  }

  return $total;
}

function GetTotalByCATID($ID_CAT) {
  global $db_prefix;

    // TO-DO: ¿De dónde sale variable $cat?
    $dbresult = db_query("
      SELECT total
      FROM {$db_prefix}articles_cat
      WHERE seotitle = '$cat'", __FILE__, __LINE__);

    $row = mysqli_fetch_assoc($dbresult);

    mysqli_free_result($dbresult);

    if ($row['total'] != -1)
      return $row['total'];
    else {
      $dbresult = db_query("
        SELECT COUNT(*) AS total
        FROM {$db_prefix}articles
        WHERE ID_CAT = $ID_CAT
        AND approved = 1", __FILE__, __LINE__);

      $row = mysqli_fetch_assoc($dbresult);
      $total = $row['total'];

      mysqli_free_result($dbresult);

      // Update the count
      $dbresult = db_query("
        UPDATE {$db_prefix}articles_cat
        SET total = $total
        WHERE seotitle = '$cat'
        LIMIT 1", __FILE__, __LINE__);

      // Return the total pictures
      return $total;
    }
}

function MyArticles() {
  global $txt, $context, $ID_MEMBER, $scripturl, $db_prefix, $modSettings, $mbname;

  is_not_guest();
  isAllowedTo('view_articles');

  $addarticle = allowedTo('add_articles');
  $context['addarticle'] = $addarticle;
  $context['editarticle'] = allowedTo('edit_articles');
  $context['deletearticle'] = allowedTo('delete_articles');

  // MyArticles
  if ($addarticle && !($context['user']['is_guest']))
    $context['articles']['buttons']['mylisting'] =  array(
      'text' => 'smfarticles_myarticles',
      'url' =>$scripturl . '?action=articles;sa=myarticles;u=' . $ID_MEMBER,
      'lang' => true,
    );

  // Search
  $context['articles']['buttons']['search'] =  array(
    'text' => 'smfarticles_search',
    'url' => $scripturl . '?action=articles;sa=search',
    'lang' => true,
  );

  $dbresult = db_query("
    SELECT COUNT(*) AS total
    FROM {$db_prefix}articles
    WHERE ID_MEMBER = $ID_MEMBER", __FILE__, __LINE__);

  $row = mysqli_fetch_assoc($dbresult);
  $total = $row['total'];

  mysqli_free_result($dbresult);

  if (isset($_REQUEST['sort']))
    $context['articles_sort'] = $_REQUEST['sort'];
  else
    $context['articles_sort'] = '';

  if (isset($_REQUEST['sorto']))
    $context['articles_sorto'] = $_REQUEST['sorto'];
  else
    $context['articles_sorto'] = '';

  if (isset($_REQUEST['sort'])) {
    switch(($_REQUEST['sort'])) {
      case 'title':
        $sort = 'a.title';
      break;
      case 'date':
        $sort = 'a.date';
      break;
      case 'rating':
        $sort = 'a.rating';
      break;
      case 'views':
        $sort = 'a.views';
      break;
      case 'username':
        $sort = 'm.realName';
      break;
      default:
        $sort = 'a.ID_ARTICLE';
    }
  }
  else
    $sort = 'a.ID_ARTICLE';

  if (isset($_REQUEST['sorto'])) {
    if ($_REQUEST['sorto'] == 'ASC')
      $sorto = 'ASC';
    else
      $sorto = 'DESC';
  }
  else
    $sorto = 'DESC';

  // Change sort order for articles
  if ($sorto == 'DESC')
    $newsorto = 'ASC';
  else
    $newsorto = 'DESC';

  $context['articles_newsorto'] = $newsorto;

  if (empty($modSettings['smfarticles_setarticlesperpage']))
    $modSettings['smfarticles_setarticlesperpage'] = 10;

  $context['start'] = (int) $_REQUEST['start'];

  // Show the articles in that category
  $dbresult = db_query("
    SELECT a.ID_ARTICLE, a.title, a.date, a.approved, a.totalratings, m.realName, a.ID_MEMBER, a.description, a.views
    FROM {$db_prefix}articles AS a
    LEFT JOIN {$db_prefix}members AS m  ON (a.ID_MEMBER = m.ID_MEMBER)
    WHERE a.ID_MEMBER = $ID_MEMBER
    ORDER BY $sort $sorto
    LIMIT $context[start], " . $modSettings['smfarticles_setarticlesperpage'], __FILE__, __LINE__);

  $context['articles_listing'] = array();

  while ($row = mysqli_fetch_assoc($dbresult))
    $context['articles_listing'][] = $row;

  mysqli_free_result($dbresult);

  $context['page_index'] = constructPageIndex($scripturl . '?action=articles;sa=myarticles', $_REQUEST['start'], $total, $modSettings['smfarticles_setarticlesperpage']);
  $context['linktree'][] = array(
    'url' => $scripturl . '?action=articles',
    'name' => $txt['smfarticles_title']
  );

  $context['sub_template'] = 'myarticles';
  $context['page_title'] = $mbname . ' - ' . $txt['smfarticles_title'] . ' - ' . $txt['smfarticles_myarticles'];
}

function Search() {
  global $txt, $context, $ID_MEMBER, $scripturl, $user_info, $db_prefix, $mbname;

  isAllowedTo('view_articles');

  $addarticle = allowedTo('add_articles');

  // MyArticles
  if ($addarticle && !($context['user']['is_guest']))
    $context['articles']['buttons']['mylisting'] =  array(
      'text' => 'smfarticles_myarticles',
      'url' =>$scripturl . '?action=articles;sa=myarticles;u=' . $ID_MEMBER,
      'lang' => true,
    );

  // Search
  $context['articles']['buttons']['search'] =  array(
    'text' => 'smfarticles_search',
    'url' => $scripturl . '?action=articles;sa=search',
    'lang' => true,
  );

  if ($context['user']['is_guest'])
    $groupid = -1;
  else
    $groupid =  $user_info['groups'][0];

  $dbresult = db_query("
    SELECT c.ID_CAT, c.title, p.view, c.ID_PARENT, c.seotitle
    FROM {$db_prefix}articles_cat as c
    LEFT JOIN {$db_prefix}articles_catperm AS p ON (p.ID_GROUP = $groupid AND c.ID_CAT = p.ID_CAT)
    ORDER BY c.title ASC", __FILE__, __LINE__);

  $context['articles_cat'] = array();

  while ($row = mysqli_fetch_assoc($dbresult)) {
    // Check if they have permission to search these categories
    if ($row['view'] == '0')
      continue;

    $context['articles_cat'][] = $row;
  }

  mysqli_free_result($dbresult);
  CreateArticlesPrettryCategory();

  $context['sub_template']  = 'search';
  $context['page_title'] = $mbname . ' - ' . $txt['smfarticles_title'] . ' - ' .$txt['smfarticles_search'];
}

function Search2() {
  global $txt, $context, $ID_MEMBER, $scripturl, $sourcedir, $db_prefix, $modSettings, $mbname;

  isAllowedTo('view_articles');

  $addarticle = allowedTo('add_articles');

  // MyArticles
  if ($addarticle && !($context['user']['is_guest']))
    $context['articles']['buttons']['mylisting'] =  array(
      'text' => 'smfarticles_myarticles',
      'url' =>$scripturl . '?action=articles;sa=myarticles;u=' . $ID_MEMBER,
      'lang' => true,
    );

  // Search
  $context['articles']['buttons']['search'] =  array(
    'text' => 'smfarticles_search',
    'url' => $scripturl . '?action=articles;sa=search',
    'lang' => true,
  );

  if (!isset($_REQUEST['q'])) {
    if (isset($_REQUEST['cat']))
      $cat = (int) $_REQUEST['cat'];
    else
      $cat = 0;

    // Probably a normal Search
    if (empty($_REQUEST['searchfor']))
      fatal_error($txt['smfarticles_error_no_search'], false);

    $searchfor =  htmlspecialchars($_REQUEST['searchfor'], ENT_QUOTES);

    if (strlen(trim($searchfor)) <= 3)
      fatal_error($txt['smfarticles_error_search_small'], false);

    // Check the search options
    $searchtitle = isset($_REQUEST['searchtitle']) ? 1 : 0;
    $searchdescription =  isset($_REQUEST['searchdescription']) ? 1 : 0;
    $daterange = (int) $_REQUEST['daterange'];
    $memid = 0;

    // Check if searching by member id
    if (!empty($_REQUEST['pic_postername'])) {
      $pic_postername = str_replace('"', '', $_REQUEST['pic_postername']);
      $pic_postername = str_replace("'", '', $pic_postername);
      $pic_postername = str_replace('\\', '', $pic_postername);
      $pic_postername = htmlspecialchars($pic_postername, ENT_QUOTES);

      $dbresult = db_query("
        SELECT realName, ID_MEMBER
        FROM {$db_prefix}members
        WHERE realName = '$pic_postername'
        OR memberName = '$pic_postername'
        LIMIT 1", __FILE__, __LINE__);

      $row = mysqli_fetch_assoc($dbresult);

      mysqli_free_result($dbresult);

      if (db_affected_rows() != 0)
        $memid = $row['ID_MEMBER'];
    }

    $context['catwhere'] = '';

    if ($cat != 0)
      $context['catwhere'] = "p.ID_CAT = $cat AND ";

    // Check if searching by member id
    if ($memid != 0)
      $context['catwhere'] .= "p.ID_MEMBER = $memid AND ";

    // Date Range check
    if ($daterange!= 0) {
      $currenttime = time();
      $pasttime = $currenttime - ($daterange * 24 * 60 * 60);
      $context['catwhere'] .=  "(p.date BETWEEN '" . $pasttime . "' AND '" . $currenttime . "')  AND";
    }

    $s1 = 1;
    $searchquery = '';

    if ($searchtitle)
      $searchquery = "p.title LIKE '%$searchfor%' ";
    else
      $s1 = 0;

    if ($searchdescription) {
      if ($s1 == 1)
        $searchquery = "p.title LIKE '%$searchfor%' OR p.description LIKE '%$searchfor%'";
      else
        $searchquery = "p.description LIKE '%$searchfor%'";
    }

    if ($searchquery == '')
      $searchquery = "p.title LIKE '%$searchfor%' ";

    $context['smfarticles_search_query'] = $searchquery;
    $context['smfarticles_search'] = $searchfor;
  } else {
    // Check for Security check
    if (empty($_REQUEST['qs']))
      fatal_error($txt['smfarticles_err_checkfailed'],true);

    // Get the security checks
    $qs = $_REQUEST['qs'];
    $ws = $_REQUEST['ws'];

    // Verify the security checks
    $qs2 = sha1($_REQUEST['q'] . $sourcedir);
    $ws2 = sha1($_REQUEST['w'] . $sourcedir);

    if ($qs != $qs2)
      fatal_error($txt['smfarticles_err_checkfailed'], true);

    if (!empty($_REQUEST['w'])) {
      if ($ws != $ws2)
        fatal_error($txt['smfarticles_err_checkfailed'], true);
    }

    // We are passing over mutiple search results pages
    $context['catwhere'] = base64_decode($_REQUEST['w']);
    $context['articles_search_query'] =  base64_decode($_REQUEST['q']);
  }

  // Now actually do the results
  $smfarticles_where = '';

  if (isset($context['catwhere']))
    $smfarticles_where = $context['catwhere'];

  $dbresult = db_query("
    SELECT p.ID_ARTICLE
    FROM {$db_prefix}articles as p 
    WHERE  " . $smfarticles_where . " p.approved = 1
    AND (" . $context['smfarticles_search_query'] . ")", __FILE__, __LINE__);

  $numrows = mysqli_num_rows($dbresult);

  mysqli_free_result($dbresult);

  $context['start'] = (int) $_REQUEST['start'];

  $dbresult = db_query("
    SELECT p.ID_ARTICLE, p.title, p.date, p.totalratings, m.realName, p.ID_MEMBER, p.description, p.views
    FROM {$db_prefix}articles as p
    LEFT JOIN {$db_prefix}members AS m ON (m.ID_MEMBER = p.ID_MEMBER)
    WHERE  " . $smfarticles_where . " p.approved = 1
    AND (" . $context['smfarticles_search_query'] . ")
    LIMIT $context[start], " . $modSettings['smfarticles_setarticlesperpage'], __FILE__, __LINE__);

  $context['articles_listing'] = array();

  while ($row = mysqli_fetch_assoc($dbresult))
    $context['articles_listing'][] = $row;

  mysqli_free_result($dbresult);

  $q =  base64_encode($context['smfarticles_search_query']);
  $w = base64_encode($smfarticles_where);

  // Security check to make sure it has not been altered
  $qs = sha1($q . $sourcedir);
  $ws = sha1($w . $sourcedir);

  $context['page_index'] = constructPageIndex($scripturl . '?action=articles;sa=search2;q=' . $q . ';w=' . $w . ';qs=' . $qs . ';ws=' . $ws, $_REQUEST['start'], $numrows, $modSettings['smfarticles_setarticlesperpage']);
  $context['sub_template'] = 'search_results';
  $context['page_title'] = $mbname . ' - ' . $txt['smfarticles_title'] . ' - ' .$txt['smfarticles_searchresults'];
}

function ShowSubCats() {}

function ViewArticle() {
  global $txt, $context, $ID_MEMBER, $scripturl, $db_prefix, $user_info;

  // Check if the current user can view the articles list
  isAllowedTo('view_articles');

  $m_cats = allowedTo('articles_admin');
  $context['m_cats'] = $m_cats;

  if (!isset($_REQUEST['article']))
    fatal_error($txt['smfarticles_noarticleselected']);

  $article = (int) $_REQUEST['article'];

  if ($context['user']['is_guest'])
    $groupid = -1;
  else
    $groupid =  $user_info['groups'][0];

  // Show the articles in that category
  $dbresult = db_query("
    SELECT
        a.ID_ARTICLE, a.title, a.date, p.view, a.approved, a.rating, a.totalratings, m.realName, a.ID_MEMBER,
        a.description, a.views, a.ID_CAT
    FROM {$db_prefix}articles AS a
    LEFT JOIN {$db_prefix}members AS m ON (a.ID_MEMBER = m.ID_MEMBER)
    LEFT JOIN {$db_prefix}articles_catperm AS p ON (p.ID_GROUP = $groupid AND a.ID_CAT = p.ID_CAT)
    WHERE a.ID_ARTICLE = $article
    LIMIT 1", __FILE__, __LINE__);

  if (mysqli_num_rows($dbresult) == 0)
    fatal_error($txt['smfarticles_err_article_not_found'], false);

  $row = mysqli_fetch_assoc($dbresult);
  $context['article'] = $row;

  mysqli_free_result($dbresult);

  // Check Approval
  if ($row['approved'] == 0 && $m_cats == false && $row['ID_MEMBER'] != $ID_MEMBER)
    fatal_error($txt['smfarticles_err_articlenotapproved'], false);

  // Check if article is allowed to be viewed
  if ($row['view'] == '0' && ($m_cats == false && $row['ID_MEMBER'] != $ID_MEMBER))
    fatal_error($txt['smfarticles_perm_no_view_article'], false);

  $addarticle = allowedTo('add_articles');

  // MyArticles
  if ($addarticle && !($context['user']['is_guest']))
    $context['articles']['buttons']['mylisting'] =  array(
      'text' => 'smfarticles_myarticles',
      'url' =>$scripturl . '?action=articles;sa=myarticles;u=' . $ID_MEMBER,
      'lang' => true,
    );

  // Search
  $context['articles']['buttons']['search'] =  array(
    'text' => 'smfarticles_search',
    'url' => $scripturl . '?action=articles;sa=search',
    'lang' => true,
  );

  // View Article Buttons
  // Edit Article
  if ($m_cats == true  || $row['ID_MEMBER'] == $ID_MEMBER)
    $context['articles']['view_article']['edit'] =  array(
      'text' => 'smfarticles_txtedit3',
      'url' =>$scripturl . '?action=articles;sa=editarticle&id=' . $article,
      'lang' => true,
    );

  // Delete Article
  if ($m_cats == true  || $row['ID_MEMBER'] == $ID_MEMBER)
    $context['articles']['view_article']['delete'] =  array(
      'text' => 'smfarticles_txtdel3',
      'url' => $scripturl . '?action=articles;sa=deletearticle&id=' . $article,
      'lang' => true,
    );

  GetParentLink($context['article']['ID_CAT']);

  $dbresult = db_query("
    SELECT pagetext
    FROM {$db_prefix}articles_page
    WHERE ID_ARTICLE = $article
    LIMIT 1", __FILE__, __LINE__);

  $row2 = mysqli_fetch_assoc($dbresult);
  $context['article_page'] = $row2;

  mysqli_free_result($dbresult);

  if (empty($_SESSION['ultimo_articulo_visto']) || $_SESSION['ultimo_articulo_visto'] != $article) {
    db_query("
      UPDATE {$db_prefix}articles
      SET views = views + 1
      WHERE ID_ARTICLE = $article
      LIMIT 1", __FILE__, __LINE__);

    $_SESSION['ultimo_articulo_visto'] = $article;
  }

  $context['page_title'] = 'Ayuda';
  $context['sub_template']  = 'viewarticle';
}

function UpdateCategoryTotals($ID_CAT) {
  global $db_prefix;

  $dbresult = db_query("
    SELECT COUNT(*) AS total
    FROM {$db_prefix}articles
    WHERE ID_CAT = $ID_CAT
    AND approved = 1", __FILE__, __LINE__);

  $row = mysqli_fetch_assoc($dbresult);
  $total = $row['total'];

  mysqli_free_result($dbresult);

  // Update the count
  $dbresult = db_query("
    UPDATE {$db_prefix}articles_cat
    SET total = $total
    WHERE ID_CAT = $ID_CAT
    LIMIT 1", __FILE__, __LINE__);
}

function UpdateCategoryTotalByArticleID($id) {
  global $db_prefix;

  $dbresult = db_query("
    SELECT ID_CAT
    FROM {$db_prefix}articles
    WHERE ID_ARTICLE = $id", __FILE__, __LINE__);

  $row = mysqli_fetch_assoc($dbresult);

  mysqli_free_result($dbresult);
  UpdateCategoryTotals($row['ID_CAT']);
}

function DoToolBarStrip($button_strip, $direction) {
  global $settings, $txt;

  if (!empty($settings['use_tabs'])) {
    template_button_strip($button_strip, $direction);
  } else {
    echo '<td>';

    foreach ($button_strip as $tab) {
      echo '<a href="' . $tab['url'] . '">' . $txt[$tab['text']] . '</a>';

      if (empty($tab['is_last']))
        echo ' | ';
    }

    echo '</td>';
  }
}

function GetParentLink($ID_CAT) {
  global $db_prefix, $context, $scripturl;

  if ($ID_CAT == 0)
    return;

  $dbresult1 = db_query("
    SELECT ID_PARENT, title
    FROM {$db_prefix}articles_cat
    WHERE ID_CAT = $ID_CAT LIMIT 1", __FILE__, __LINE__);

  $row1 = mysqli_fetch_assoc($dbresult1);

  mysqli_free_result($dbresult1);
  GetParentLink($row1['ID_PARENT']);

  $context['linktree'][] = array(
    'url' => $scripturl . '?action=articles;cat=' . $ID_CAT,
    'name' => $row1['title']
  );
}

function RecountArticleTotals() {
  global $db_prefix;

  isAllowedTo('articles_admin');

  $dbresult1 = db_query("
    SELECT ID_CAT
    FROM {$db_prefix}articles_cat", __FILE__, __LINE__);

  while ($row = mysqli_fetch_assoc($dbresult1)) {
    UpdateCategoryTotals($row['ID_CAT']);
  }

  mysqli_free_result($dbresult1);
  redirectexit('action=articles;sa=admin');
}

function CreateArticlesPrettryCategory() {
  global $context;

  $finalArray = array();
  $parentList = array(0);
  $newParentList = array();
  $spacer = 0;

  for ($g = 0; $g < count($parentList); $g++) {
    $tmpLevelArray = array();

    for ($i = 0;$i < count($context['articles_cat']); $i++) {
      if ($context['articles_cat'][$i]['ID_PARENT'] == $parentList[$g]) {
        $newParentList[] = $context['articles_cat'][$i]['ID_CAT'];
        $newParentList = array_unique($newParentList);
        $context['articles_cat'][$i]['title'] = str_repeat('-', $spacer) . $context['articles_cat'][$i]['title'];
        $tmpLevelArray[] = $context['articles_cat'][$i];
      }
    }

    // Check Top Level ID_PARENT
    if ($parentList[$g] == 0) {
      $finalArray = $tmpLevelArray;
    } else {
      $tmpArray2 = array();

      for ($j = 0; $j < count($finalArray); $j++) {
        $tmpArray2[] = $finalArray[$j];

        // Find Parent good Now we just insert the records that we found right after the parent
        if ($finalArray[$j]['ID_CAT'] == $parentList[$g]) {
          for ($z = 0; $z < count($tmpLevelArray); $z++) {
            $tmpArray2[] = $tmpLevelArray[$z];
          }
        }
      }

      $finalArray = $tmpArray2;
    }

    $tmpLevelArray = array();

    if ($g == (count($parentList) -1) && !empty($newParentList)) {
      $parentList = array();
      $parentList = $newParentList;
      $newParentList = array();
      $g =- 1;
      $spacer++;
    } else if ($g == (count($parentList) -1) && empty($newParentList)) {

    }
  }

  $context['articles_cat'] = array();
  $context['articles_cat'] = $finalArray;
}

?>