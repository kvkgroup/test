<?php

use yii\db\Migration;

/**
 * Class m200803_111503_fill
 */
class m200803_111503_fill extends Migration
{
    /**
     * Lorem Ipsum text
     * @var string
     */
    protected $text = "
What is Lorem Ipsum?
Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.
Why do we use it?
It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using 'Content here, content here', making it look like readable English. Many desktop publishing packages and web page editors now use Lorem Ipsum as their default model text, and a search for 'lorem ipsum' will uncover many web sites still in their infancy. Various versions have evolved over the years, sometimes by accident, sometimes on purpose (injected humour and the like).
Where does it come from?
Contrary to popular belief, Lorem Ipsum is not simply random text. It has roots in a piece of classical Latin literature from 45 BC, making it over 2000 years old. Richard McClintock, a Latin professor at Hampden-Sydney College in Virginia, looked up one of the more obscure Latin words, consectetur, from a Lorem Ipsum passage, and going through the cites of the word in classical literature, discovered the undoubtable source. Lorem Ipsum comes from sections 1.10.32 and 1.10.33 of \"de Finibus Bonorum et Malorum\" (The Extremes of Good and Evil) by Cicero, written in 45 BC. This book is a treatise on the theory of ethics, very popular during the Renaissance. The first line of Lorem Ipsum, \"Lorem ipsum dolor sit amet..\", comes from a line in section 1.10.32.
The standard chunk of Lorem Ipsum used since the 1500s is reproduced below for those interested. Sections 1.10.32 and 1.10.33 from \"de Finibus Bonorum et Malorum by Cicero are also reproduced in their exact original form, accompanied by English versions from the 1914 translation by H. Rackham.
Where can I get some?
There are many variations of passages of Lorem Ipsum available, but the majority have suffered alteration in some form, by injected humour, or randomised words which don't look even slightly believable. If you are going to use a passage of Lorem Ipsum, you need to be sure there isn't anything embarrassing hidden in the middle of text. All the Lorem Ipsum generators on the Internet tend to repeat predefined chunks as necessary, making this the first true generator on the Internet. It uses a dictionary of over 200 Latin words, combined with a handful of model sentence structures, to generate Lorem Ipsum which looks reasonable. The generated Lorem Ipsum is therefore always free from repetition, injected humour, or non-characteristic words etc.";

    /**
     * Lorem Ipsum words
     * @var string[]
     */
    protected $words = [];

    /**
     * Overall items limit
     * @var int
     */
    public $overallLimit = 500;

    /**
     * Local items limit
     * @var int
     */
    public $localLimit = 500;

    /**
     * items processed count
     * @var int
     */
    public $processed = 0;

    /**
     * Minimal child node count
     * @var int
     */
    public $minimalChilds = 10;

    /**
     * {@inheritdoc}
     */
    public function Init(){
        parent::Init();
        $this->words = str_word_count($this->text, 1);
        $this->words = array_unique($this->words);
        $this->words = array_filter($this->words, function($word){
            return mb_strlen($word) >= 3;
        });
    }

    /**
     * {@inheritdoc}
     */
    public function Up()
    {
        echo("Create nodes...".PHP_EOL);
        $this->truncateTable(\app\models\Tree::tableName());
        while ($this->processed < $this->overallLimit) {
            $nodeParent = $this->getRandomNode();
            $childCount = rand($this->minimalChilds, $this->getListCount());
            $childCount = (($this->getListCount() - $childCount) < 2*$this->minimalChilds)?$this->getListCount():$childCount;
            echo("Add {$childCount} childs to #{$nodeParent->id} node...".PHP_EOL);
            for($i=0; $i<$childCount; $i++){
                echo("Add {$i} of {$childCount} node...".PHP_EOL);
                $node = new \app\models\Tree();
                $node->active = true;
                $node->icon_type = 1;
                $node->name = $this->getRandomWord();
                $node->removable_all = 1;
                $node->removable = 1;
                $node->collapsed = ($nodeParent->lvl > 0)?1:0;
                $node->appendTo($nodeParent);
                $node->save();
                $this->processed++;
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function Down()
    {
        $this->truncateTable(\app\models\Tree::tableName());
    }

    /**
     * Return random word from text
     * @return string
     */
    protected function getRandomWord(){
        $randKey = array_rand($this->words);
        return $this->words[$randKey];
    }

    /**
     * Count nodes last do add
     * @return int
     */
    protected function getListCount(){
        return $this->overallLimit - $this->processed;
    }

    /**
     * Retrun random node model
     * @return \app\models\Tree
     */
    protected function getRandomNode(){
        $node = \app\models\Tree::find()->orderBy(new \yii\db\Expression('rand()'))->one();
        if (!$node){
            // First node

            echo("Create root node...".PHP_EOL);
            $node = new \app\models\Tree();
            $node->active = true;
            $node->icon_type = 1;
            $node->name = $this->getRandomWord();
            $node->removable_all = 1;
            $node->removable = 1;
            $node->makeRoot();
            $node->save();
            $this->processed++;
        }
        return $node;
    }
}
