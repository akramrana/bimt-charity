<?php
/* @var $this yii\web\View */

use yii\helpers\Html;

$this->title = 'Privacy Policy';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="container-fluid">
    <div class="login-logo">
        <a href="#"><b>BIMT</b>Charity Foundation</a>
    </div>
    <!-- /.login-logo -->
    <div class="login-box-body">
        <div class="site-about">
            <p>
                <?php
                echo $model->content;
                ?>
            </p>
        </div>
    </div>
</div>