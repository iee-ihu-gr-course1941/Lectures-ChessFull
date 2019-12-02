$(function () {
	draw_empty_board();
	fill_board();
	$('#do_move').click(do_move);
	$('#chess_reset').click(do_reset);
});


function draw_empty_board() {
	var t='<table id="chess_table">';
	for(var i=8;i>0;i--) {
		t += '<tr>';
		t += '<td class="line_label">'+i+'</td>';
		for(var j=1;j<9;j++) {
			t += '<td class="chess_square" id="square_'+j+'_'+i+'">' + j +','+i+'</td>'; 
		}
		t+='</tr>';
	}
	t += '<tr><td class="column_label line_label"></td>';
	for(var j=1;j<9;j++) {
		t += '<td class="column_label">'+j+'</td>';
	}
	t+='</tr>';
	t+='</table>';
	
	$('#chess_board').html(t);
}

function fill_board() {
	$.ajax({url: "chess.php/board/", success: fill_board_by_data });
	
}

function fill_board_by_data(data) {
	for(var i=0;i<data.length;i++) {
		var o = data[i];
		var id = '#square_'+ o.x +'_' + o.y;
		var c = (o.piece!=null)?o.piece_color + o.piece:'';
		var im = (o.piece!=null)?'<img class="piece" src="images/'+c+'.png">':'';
		$(id).addClass(o.b_color+'_square').html(im);
		
	}
}
function display_error_move(data){
	alert(data.responseJSON.error);
}
function do_move(e) {
	var s = $('#nextmove').val();
	var sa = s.split(' ');
	var x=sa[0];
	var y=sa[1];
	var x1=sa[2];
	var y1=sa[3];
	var args = {x: x1, y: y1};
	var a = JSON.stringify(args);
	$.ajax({url: "chess.php/board/piece/"+x+'/'+y+'/', method: 'PUT', 
			data : a,
			headers: { "Content-Type": "application/json"}, 
			success: fill_board_by_data,
			error: display_error_move
			});
}

function do_reset(e) {
	$.ajax({url: "chess.php/board/", method: 'POST', 
			success: fill_board_by_data,
			error: display_error_move
			});
	
}
