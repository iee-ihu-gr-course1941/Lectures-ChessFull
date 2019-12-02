<?php

function show_board() {
	global $mysqli;
	$sql = 'select * from board';
	$st = $mysqli->prepare($sql);

	$st->execute();
	$res = $st->get_result();
	
	header('Content-type: application/json');
	print json_encode($res->fetch_all(MYSQLI_ASSOC), JSON_PRETTY_PRINT);
}

function reset_board() {
	global $mysqli;
	
	$sql = 'call clean_board()';
	$mysqli->query($sql);
	show_board();
	
}
function show_board_pieces($points) {
	global $mysqli;
	$sql = 'select * from board where x=? and y=?';
	$st = $mysqli->prepare($sql);	
	$st->bind_param('ii',$x,$y);
	$ar = [];
	foreach ($points as $k=>$v) {
		$x = $v['x'];
		$y = $v['y'];
		$st->execute();
		$res = $st->get_result();
		array_push($ar,$res->fetch_assoc());
	}
	
	header('Content-type: application/json');
	print json_encode($ar, JSON_PRETTY_PRINT);
}
function show_board_piece($x,$y) {
	show_board_pieces([[ 'x'=>$x, 'y'=>$y]]);
}
function move_board_piece($x,$y, $x1, $y1) {
	global $mysqli;
	
	$sql = 'select * from board where x=? and y=?';
	$st = $mysqli->prepare($sql);	
	$st->bind_param('ii',$x,$y);
	$st->execute();
	$res = $st->get_result();
	$row1 = $res->fetch_assoc();
	
	$st->bind_param('ii',$x1,$y1);
	$st->execute();
	$res = $st->get_result();
	$row2 = $res->fetch_assoc();
	
	if($row1['piece']==null || $row1['piece']=='') {
		header("HTTP/1.1 400 Bad Request");
		header('Content-type: application/json');
		print json_encode(['error'=>'Δεν υπάρχει πιόνι στο σημείο έναρξης.']);
		return;
	}
	if($row1['piece_color']==$row2['piece_color'] ) {
		header("HTTP/1.1 400 Bad Request");
		header('Content-type: application/json');
		print json_encode(['error'=>'Υπάρχει άλλο πιόνι στο σημείο προορισμού.']);
		return;
	}
		
	$sql = 'update board set piece_color=?, piece=? where x=? and y=?';
	$st2 = $mysqli->prepare($sql);
	$pc=null;
	$pp=null;
	$st2->bind_param('ssii',$pc,$pp,$x,$y);
	$st2->execute();
	$pc=$row1['piece_color'];
	$pp=$row1['piece'];
	$st2->bind_param('ssii',$pc,$pp,$x1,$y1);
	$st2->execute();
	show_board_pieces([[ 'x'=>$x, 'y'=>$y], [ 'x'=>$x1, 'y'=>$y1]]);
}



?>
