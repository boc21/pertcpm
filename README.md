# pertcpm
some almost-OO php

Use a couple BFS (Breadth First Search) on a graph data structure of project steps- find out the ES, EF, LS, and LFs (Early Start|Finish, Late Start|Finish) for each node and the slack for the steps- the nodes with zero slack are the critical path. 

PERT (Project Evaluation and Review Technique) and CPM (Critical Path Management) are straight out of the PMI (Project Management Institute) book handy for a project+ certification. Or a spreadsheet can do the same work, probably much quicker than it takes this author to figure out some (sloppy) php code but not nearly as much fun.

The GraphViz package makes it really easy to get an image of the graph, after setting up the arrays for the nodes with weights and the dependancies the visual makes for better spotting of a not quite correct graph. Has to be an acyclic graph, no loops allowed or the BFSs would probably go infinite loop. Un Good. 

Yank the 'z' node from the code for something closer to the House project snippet like at <a href="http://boc21first.blogspot.com/p/graphviz.html">a GraphViz page</a>

The package prereqs could use an update, this author finally got a good run of pert_z.php on a clean Fedora 24 after:

  sudo dnf install graphviz-php
  sudo dnf install php-cli
  sudo dnf install php-pear
