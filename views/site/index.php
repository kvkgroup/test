<?php

/* @var $this yii\web\View */

use kartik\tree\TreeView;

$this->title = 'Тестовое задание';
\yii\widgets\Pjax::begin(['id' => 'pjaxContent']);
?>
<div class="site-index">
        <?= TreeView::widget([
            'query'             => \app\models\Tree::find()->addOrderBy('root, lft'),
            'headingOptions'    => ['label' => ''],
            'rootOptions' => ['label'=>'<span class="text-primary">Корень</span>'],
            'isAdmin'           => true,                       // optional (toggle to enable admin mode)
            'fontAwesome'       => true,
            'cacheSettings'     => ['enableCache' => false],
            'topRootAsHeading'  => false,
            'softDelete'        => false,
            'iconEditSettings'  => [
                'show' => 'list',
                'listData' => [
                    'folder' => 'Folder',
                    'file' => 'File',
                    'mobile' => 'Phone',
                    'bell' => 'Bell',
                ]
            ],
        ]); ?>
</div>
<?php \yii\widgets\Pjax::end(); ?>
<?php

$this->registerJs(<<<JS
    var pos = 0;
    $(document).ready(function(){
        $(document).on('pjax:success', function(){
            $('.kv-tree-container').scrollTop(pos);
            initView();
        });
        initView();
    });

    function initView(){
        $('.kv-tree li .kv-node-label').draggable({ revert: 'invalid' });
        $('.kv-tree li .kv-node-label, .kv-tree-root').droppable({
            classes: {
                "ui-droppable-hover": "ui-state-hover"
            },
            accept: 'li .kv-node-label',
            drop: function( event, ui ) {
                pos = $('.kv-tree-container').scrollTop();
                $.post('/site/move', {
                    id: $(ui.draggable).parents('li').data('key'),
                    to: $(this).parents('li').data('key')
                }, function(){
                    $.pjax.reload({container: '#pjaxContent'});
                });
          }
        })
    }
JS
);