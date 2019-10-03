<?php

use yii\helpers\Html;

/* @var $this \yii\web\View */
/* @var $content string */
?>

<header class="main-header">

    <?= Html::a('<span class="logo-mini">BIMT CHARITY Foundation</span><span class="logo-lg">' . Yii::$app->name . '</span>', Yii::$app->homeUrl, ['class' => 'logo']) ?>

    <nav class="navbar navbar-static-top" role="navigation">

        <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
            <span class="sr-only">Toggle navigation</span>
        </a>

        <div class="navbar-custom-menu">

            <ul class="nav navbar-nav">
                <!-- User Account: style can be found in dropdown.less -->

                <li class="dropdown user user-menu">
                    <?=
                    Html::a(
                            '<i class="fa fa-sign-out"></i>Sign out',
                            ['/site/logout'],
                            ['data-method' => 'post', 'class' => 'btn btn-purple btn-flat pull-right']
                    )
                    ?>
                    <a href="<?php echo \yii\helpers\Url::to(['site/edit-profile']) ?>" class="btn btn-purple btn-flat pull-right">My Profile</a>
                </li>
            </ul>
        </div>
    </nav>
</header>
