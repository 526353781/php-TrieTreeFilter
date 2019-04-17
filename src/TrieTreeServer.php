<?php
/**
 * Created by PhpStorm.
 * User: zhangjunjie
 * Date: 2019/4/15
 * Time: 12:05 PM
 */
namespace MadDog\TrieTree;

class TrieTreeServer {
    private $node;
    private $filterChars = array();//匹配到的关键词

    public function __construct()
    {
        if (!isset($this->node)) {
            $this->node = new TrieTreeNode();
        }
    }

    public static function create()
    {
        return new static;
    }

    /**
     * 构建一颗tree
     * @param string $str
     * @return null|TrieNode
     */
    public function inset(string $str)
    {
        $len  = mb_strlen($str);
        $node =  $this->node;

        for ($i=0; $i<$len; $i++) {
            $char = mb_substr($str, $i, 1);

            $filterList = $node->findNode($char);

            if (is_null($filterList)) {//如果下层没有节点,新建节点往下
                $filterList = new TrieTreeNode($char);
                $node->add($filterList);
            }

            $node = $filterList;//如果下层有节点,顺着节点往下
        }

        $node->setEnd();

        return $node;
    }

    /**
     *
     * @param $str
     * @return bool
     */
    public function filter($str)
    {
        $len = mb_strlen($str);

        $node = $this->node;

        for ($i=0,$tempI = 1; $i<$len; $i++) {
            $char =  mb_substr($str, $i, 1);

            $nextNode = $node->findNode($char);

            if (isset($nextNode)) {
                $this->filterChars[] = $nextNode->char();
                $tempI = $i;

                $node = $nextNode;//节点下移
                if ($node->isEnd()) {
                    return true;
                }

            } else {
                if (!is_null($this->filterChars)) {//之前有同步,回滚到同部位重新查找
                    $i = $tempI;
                    unset($this->filterChars);
                }
                $node = $this->node;//重置树
            }

        }

        return false;
    }


}