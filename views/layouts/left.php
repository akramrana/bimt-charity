<aside class="main-sidebar">
    <section class="sidebar">
        <!-- Sidebar user panel -->
        <div class="user-panel" style="height: 60px;">
            <div class="pull-left info" style="left: 0px;">
                <p><?= Yii::$app->user->identity->fullname; ?></p>
                <a href="<?php echo \yii\helpers\Url::to(['site/edit-profile']) ?>"><i class="fa fa-circle text-success"></i> Online</a>
            </div>
        </div>

        <?php
        $userSearch = Yii::$app->request->get('UserSearch', []);
        //debugPrint(Yii::$app->user->identity);
        echo dmstr\widgets\Menu::widget(
                [
                    'options' => ['class' => 'sidebar-menu tree', 'data-widget' => 'tree'],
                    'items' => [
                        ['label' => 'NAVIGATION', 'options' => ['class' => 'header']],
                        ['label' => 'Dashboard', 'icon' => 'dashboard', 'url' => ['site/index']],
                        ['label' => 'Invoices', 'icon' => 'fa fa-book', 'url' => ['monthly-invoice/index']],
                        ['label' => 'Sadaqah', 'icon' => 'fa fa-plus', 'url' => ['payment-received/index'],],
                        ['label' => 'Fund Request', 'icon' => 'fa fa-pie-chart', 'url' => ['fund-request/index']],
                        ['label' => 'Donation', 'icon' => 'share', 'url' => ['payment-release/index']],
                        ['label' => 'Expenses', 'icon' => 'fa fa-money', 'url' => ['expense/index']],
                        [
                            'label' => 'Members',
                            'icon' => 'fa fa-users',
                            //'url' => ['user/index'],
                            'items' => [
                                [
                                    'label' => 'Actively Donating',
                                    'icon' => 'check',
                                    'url' => ['user/index', 'UserSearch[is_active_donor]' => 1],
                                    'active' => Yii::$app->controller->route === 'user/index' && isset($userSearch['is_active_donor']) && (int) $userSearch['is_active_donor'] === 1,
                                ],
                                [
                                    'label' => 'Not Actively Donating',
                                    'icon' => 'close',
                                    'url' => ['user/index', 'UserSearch[is_active_donor]' => 0],
                                    'active' => Yii::$app->controller->route === 'user/index' && isset($userSearch['is_active_donor']) && (int) $userSearch['is_active_donor'] === 0,
                                ],
                                [
                                    'label' => 'Waiting for Approval',
                                    'icon' => 'hourglass',
                                    'url' => ['user/index', 'UserSearch[is_approved]' => 0],
                                    'active' => Yii::$app->controller->route === 'user/index' && isset($userSearch['is_approved']) && (int) $userSearch['is_approved'] === 0,
                                ],
                                [
                                    'label' => 'Special Member',
                                    'icon' => 'star',
                                    'url' => ['user/index', 'UserSearch[is_exception]' => 1],
                                    'active' => Yii::$app->controller->route === 'user/index' && isset($userSearch['is_exception']) && (int) $userSearch['is_exception'] === 1,
                                ],
                                [
                                    'label' => 'All Member',
                                    'icon' => 'list',
                                    'url' => ['user/index'],
                                    'active' => Yii::$app->controller->route === 'user/index' && empty($userSearch),
                                ],
                            ],
                        ],
                        ['label' => 'Documents', 'icon' => 'fa fa-file', 'url' => ['document/index']],
                        ['label' => 'Activity Log', 'icon' => 'fa fa-bell', 'url' => ['notification/index']],
                        ['label' => 'Report', 'icon' => 'fa fa-bar-chart', 'url' => ['report/index']],
                        [
                            'label' => 'Send Mail',
                            'icon' => 'fa fa-envelope',
                            'url' => ['site/send-mail'],
                            'visible' => (Yii::$app->user->identity->user_type == 'S') ? true : false,
                        ],
                    ],
                ]
        )
        ?>

    </section>

</aside>
