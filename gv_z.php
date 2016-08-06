<?php
/*

gv_z.php

draw/render a graph data structure with pear and GraphViz

req: php
req: httpd
req: php-pear-Image-GraphViz

see http://www.graphviz.org and

Borate, S. (February 13, 2012). Building a graph data structure in PHP. [Web log
    comment].  Retrieved from
    http://www.codediesel.com/algorithms/building-a-graph-data-structure-in-php/
    
*/

require_once 'Image/GraphViz.php';

// make a directed acyclic graph render it Left to Right (LR)
$dag =& new Image_GraphViz(true,
                           array('rankdir' => 'LR'
                                ,'label' => 'A Project Paths'
                                )
                          ) ;

// array of two element arrays the first [0] element values must be unique
$aNodes = array( [ 'z', 0.5 ],
                 [ 'a', 1.0 ],
                 [ 'b', 3.0 ],
                 [ 'c', 2.0 ],
                 [ 'd', 1.0 ], ) ;

foreach($aNodes as $node) {
 
  // add node to the graph with name and weight values
  $dag->addNode( $node[0],
                 array( 'label' =>  "$node[0]<br />" . $node[1], ) );
}

// connections between different nodes
//   'a-b' -> means node 'a' connects with node 'b'
$aVertices = array('z-b', 'a-b', 'a-c', 'b-d', 'c-d', );

// optional array of critical path vertices|edges
$aCritpath = array( 'a-b','b-d' );

foreach($aVertices as $vertex) {

  // go basic
  $colr = "black" ;
  $arro = "normal" ;

/*  // or optionally pretty-up the drawing
  // $aCritpath array must be enabled
*/
  // check if this is a critical path vertex|edge
  if ( in_array( $vertex, $aCritpath ) ) {
    $colr = "red" ;
    $arro = "empty" ;
  } else {
    $colr = "black" ;
    $arro = "normal" ;
  }

  // split out the two edges
  $data = preg_split("/-/", $vertex);
  // 
  $dag->addEdge( array( $data[0] => $data[1], ),
                 array( 'color'  => $colr,
                        'arrowhead' => $arro, ) );
}

// disable the ...->image() call to see any `echo..` or `print(...` text
// can not do both. there is always a trade-off.

// draw|render the img
$dag->image();

?>