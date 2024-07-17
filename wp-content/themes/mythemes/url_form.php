<?php

/**
 * @package mytheme
 */
?>
<?php
get_header();
$urls = get_all_saved_urls();

?>
<div class="container main">
    <div class="row">
        <div class="message text-danger mx-auto p-2 fs-3 text-center col-12 align-self-center"></div>
    </div>
    <div class="row pb-2">
        <div class="col-12 col-md-10">
            <form id="custom-meta-form" method="post" class="w-100 d-flex align-items-center" style="height: 100%">
                <input type="text" id="original_url" name="original_url" class="form-control " placeholder="Enter the original URL" />
            </form>
        </div>
        <div class="col-12 col-md-2 d-flex justify-content-end align-items-center">
            <a type="submit" id="save-button" class="btn btn-primary w-100">Зберегти</a>
        </div>
    </div>

    <div class="mytable-responsive">
        <table class="table table-dark">
            <thead>
                <tr>
                    <th scope="col">Short URL</th>
                    <th scope="col">URL</th>
                    <th scope="col"></th>
                </tr>
            </thead>
            <tbody id="tbody">

            </tbody>
        </table>

        <?php include_once 'components/modal_menu.php' ?>

    </div>
</div>
<?php get_footer(); ?>