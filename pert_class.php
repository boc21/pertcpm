<?php

// file: pert_class.php
// req: php-pear

require_once 'Structures/Graph.php';
require_once 'Structures/Graph/Node.php';

// for acyclic test and topo sort
require_once 'Structures/Graph/Manipulator/AcyclicTest.php';

class node extends Structures_Graph_Node {

  public $es = null;
  public $ef = null;
  public $ls = null;
  public $lf = null;
  public $wt = null;

  public $aPrereqs = array();

  public function setESEF( $es, $ef ) {
    $this->es = $es;
    $this->ef = $ef; 
  }
  public function setLSLF( $ls, $lf ) {
    $this->ls = $ls; 
    $this->lf = $lf; 
  }
  public function setNodeWeight ( $wt ) {
    $this->wt = $wt; 
  }
  public function getNodeWeight () {
    return $this->wt;
  }
}

class graph extends Structures_Graph {

  public $aStarts = array();
  public $starts = null;

  public $aEnds = array();
  public $aEndEFs = array();
  public $ends = null; 

  public $aPaths = array(); 
  public $paths = null;

  public function isAcyclic () {
    $t = new Structures_Graph_Manipulator_AcyclicTest();
    return ( $t->isAcyclic( $this ) );
  }

  public function countEndStart( $g ) {

    // visit all nodes in graph $g find end and start node(s)
    // must have have at least one of each
    // may have more than one of each
    // might have an orphan node

    $this->ends = 0;
    $this->starts = 0;

    // assume the best
    $orphan = false;

    $gNodes = $g->getNodes();
    foreach( $gNodes as $node ) {
      if ( $node->outDegree() == 0 ) {
        if ( $node->inDegree() == 0 ) {
          // found an orphan node 
          $orphan = true;
        } else {
          // an end node. push to $aEnds
          array_push( $this->aEnds, $node->getData() );
          // bump ends count
          $this->ends++;
        }
      }

      if ( $node->inDegree() == 0 ) {
        // a start node, push it to $aStarts
        array_push( $this->aStarts, $node->getData() );

        // bump the starts and set es, ef for node 
        $this->starts++;
        $node->setESEF( 0, $node->getNodeWeight());
      }
    }

    // valid means no orphans and ends > 0 and starts > 0 
    return ( !$orphan and $this->ends > 0 and $this->starts > 0 ) ;
  }
}

/* eof: pert_class.php */ ?>