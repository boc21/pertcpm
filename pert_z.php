#!/usr/bin/php
<?php
/*
  pert_z.php
  graph stuff using  pear Structures/Graph.php
  req: php-pear

  graph code at 

  http://www.codediesel.com/algorithms/building-a-graph-data-structure-in-php
  Borate, S. (February 19, 2012). 

*/

include 'pert_class.php';

// make a directed acyclic graph 
$dag =& new graph(true);

$aNodes = array(
 [ 'z', 0.5 ],
 [ 'a', 1.0 ],
 [ 'b', 3.0 ],
 [ 'c', 2.0 ],
 [ 'd', 1.0 ],
 );

$nodes = array();

foreach($aNodes as $node) {
  // make a new node 
  $nodes[$node[0]] =& new node();
 
  // add node to the graph 
  $dag->addNode($nodes[$node[0]]);

  // set the node name
  $nodes[$node[0]]->setData( $node[0] );

  // setMetadata for node weight
  $nodes[$node[0]]->setMetadata('node weight', $node[1] );
  // or use the node class 
  $nodes[$node[0]]->setNodeWeight( $node[1] );
}

// connections between different nodes
//   'a-b' means node 'a' is connected to node 'b'
$vertices = array('z-b', 'a-b', 'a-c', 'b-d', 'c-d', );

foreach($vertices as $vertex) {
  $data = preg_split("/-/",$vertex);
  $nodes[$data[0]]->connectTo($nodes[$data[1]]);
}

// do the start, end counts and some valid checks
if ( $dag->countEndStart($dag) ) { 

  print"no orphans. start(s), end(s) both > 0.\n";

  // graph must be acyclic
  if( $dag->isAcyclic() ) {

    // traverse all paths and set (or adjust) ES, EF for each child
    // push parent(s) prerequisite(s) for children to node->prereq
    // push EFs for end node(s)
    // must have starts and ends both > 0

    // bfs begin at start node(s)
    $aQueue = array();
    foreach( $dag->aStarts as $n ) {
      array_push( $aQueue, $nodes[$n]->getData() );
    }

    while ( count( $aQueue )) {
      // pull the next item off (of, aka remove from) the queue
      $v = array_shift( $aQueue );

      $es = $nodes[$v]->es;
      $ef = $nodes[$v]->ef;

      $conn = $nodes[$v]->getNeighbours();
      foreach( $conn as $n ) {
        if ( $n->outDegree() > 0 ) {
          array_push( $aQueue, $n->getData());
        }

        // see if the node is already in prereq 
        if ( ! in_array( $v, $n->aPrereqs, true )) {
          array_push( $n->aPrereqs, $v );
        }

        // if es is not set or es < ef
        if ( ! isset( $n->es ) or $n->es < $ef) { 
          // set es, add node weight for ef
          $n->setESEF( $ef, $ef + $n->getNodeWeight() );
        }
        // if this is an end node
        if ( $n->outDegree() == 0 ) {
          if ( count( $dag->aEndEFs ) < 1 or ( $n->ef > max( $dag->aEndEFs ) ) ) {
            // and is greter than other end EFs found
            array_push( $dag->aEndEFs, $n->ef );
          }
        }
      }
    } // end while count( $aQueue )

    // set end node LFs, LSs 
    foreach( $dag->aEnds as $n ) {
      $nodes[$n]->lf = max( $dag->aEndEFs );
      $nodes[$n]->ls = $nodes[$n]->lf - $nodes[$n]->getNodeWeight();
    }

    // another bfs start at nodes in aEnds
    $aQueue = array();
    foreach( $dag->aEnds as $n ) {
      array_push( $aQueue, $nodes[$n]->getData() );
    }

    while ( count( $aQueue )) {
      $v = array_shift( $aQueue );
      $ls = $nodes[$v]->ls;
      if ( count( $nodes[$v]->aPrereqs ) ) {
        foreach( $nodes[$v]->aPrereqs as $n ) {
          if ( $nodes[$n]->inDegree() > 0 ) {
            // not a start node, push it
            array_push( $aQueue, $nodes[$n]->getData() );
          }
          // if ls is not set or ls < lf
          if ( ! isset( $nodes[$n]->lf ) or $nodes[$n]->lf > $ls ) {
            $nodes[$n]->lf = $ls ;
            $nodes[$n]->ls = $nodes[$n]->lf - $nodes[$n]->getNodeWeight();
          }
        }
      } 
    }

    // figure out all possible paths 
    // another bfs 
    $aQueue = array();
    foreach( $dag->aStarts as $n ) {
      array_push( $aQueue, $nodes[$n]->getData() );
    }

    while ( count( $aQueue )) {
      $v = array_shift( $aQueue );
      $conn = $nodes[$v]->getNeighbours();
      foreach( $conn as $n ) {
        if ( $n->outDegree() > 0 ) {
          array_push( $aQueue, $n->getData() );
        }
      }
    }

    print "node  wt  ES  EF  LS  LF  Slack Prereq\n";
    // printf the node names and the numbers
    $node = $dag->getNodes();
    foreach( $node as $n ) {
      printf("%-4s %2.1f %2.1f %2.1f %2.1f %2.1f %6.1f ",
      $n->getData(),
      $n->getNodeWeight(),
      $n->es,
      $n->ef,
      $n->ls,
      $n->lf,
      $n->lf - $n->ef );

      // show all (any?) prerequisites
      foreach( $n->aPrereqs as $prereq ) {
        printf("%s ", $prereq);
      }
      print "\n";
    }
  } else {
    print " acyclic fail.\n";
  }
} else {
  print "orphan == 0 or starts == 0 or ends == 0.\n";
}

//eof: pert_z.php
?>