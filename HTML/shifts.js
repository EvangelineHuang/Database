/*Creator: Matt ko
Works alongside shifts.php page to create a weekly calendar of shifts for the employee to view */

var createCalendar = function(){

	var starts = [];

	var ends   = [];

	var dow	   = [];

	document.querySelectorAll(".start").forEach(function(e){
		starts.push(e.textContent);
	});
	console.log(starts);
	document.querySelectorAll(".end").forEach(function(e){
		ends.push(e.textContent);
	});
	console.log(ends);
	document.querySelectorAll(".dow").forEach(function(e){
		dow.push(e.textContent);
	});
	console.log(dow);


	var svgheight = window.innerHeight*2/3;
	var svgwidth  = window.innerWidth-230;


	var margins = {
		top:25,
		bottom:25,
		left:100,
		right:25
	};

	var width = svgwidth - margins.left - margins.right; 
	var height = svgheight - margins.top - margins.bottom;

	var svg = d3.select('svg').attr('width',svgwidth).attr('height',svgheight);


	var days = ['Mon','Teus','Wed','Thurs','Fri'];
	var week1 = ['','','','',''];
	var week2 = ['','','','',''];
	var firstday = dow[0];
	var f = firstday;
	for(var i = firstday-1; i < 5; i++){
		week1[i]=f;
		f++;
	};
	var monindex = 0;
	dow.forEach(function(d,i){ if(d==1){ monindex = i;}});
	if(monindex != 0){
		for(var x = 0; x < firstday-1; x++){
			week2[x] = dow[monindex];
			monindex++;
		}
	};
	console.log(week1);
	console.log(week2);

	var calendarplot = svg.append('g').classed('plot',true)
				.attr('tranform',function(){
					return 'translate('+margins.left+','+margins.top+')';
				});

	calendarplot.selectAll('.labelbox').data(days).enter().append('rect')
		.attr('x',function(d,i){ return i*(width/6+7);})
		.attr('y',0)
		.attr('width', function(d){ return width/6;})
		.attr('height',function(d){return height/3.5;})
		.attr('fill','#FFC5A7')
		.attr('rx',12);

	calendarplot.selectAll('.labelword').data(days).enter().append('text')
		.attr('x',function(d,i){ return i*width/6+width/15;})
		.attr('y',function(d){return height/8;})
		.text(function(d){return d;});

	calendarplot.selectAll('.weekone').data(week1).enter().append('rect')
		.attr('x',function(d,i){ return i*(width/6+7);})
		.attr('y',function(d){return height/4 + 20;})
		.attr('width', function(d){ return width/6;})
		.attr('height',function(d){return height/3.5;})
		.attr('fill',function(d,i){
			if(d != ''){

				calendarplot.append('text').classed('starttime'+i,true)
					.attr('x',function(){ return i*(width/6+7)+10;})
					.attr('y',function(d){return height/4 + height/8;})
					.text(starts[i].substr(11,18))
					.attr('fill','white');

				calendarplot.append('text').classed('endtime'+i,true)
					.attr('x',function(){ return i*(width/6+7)+10;})
					.attr('y',function(d){return height/4 + 2*height/8;})
					.text(ends[i].substr(11,18))
					.attr('fill','white');




				return 'green';
			}
			else{return '#FFC5A7';}
		})
		.attr('rx',12);

	calendarplot.selectAll('.week2').data(week2).enter().append('rect')
		.attr('x',function(d,i){ return i*(width/6+7);})
		.attr('y',function(d){return height/2 + 40;})
		.attr('width', function(d){ return width/6;})
		.attr('height',function(d){return height/3.5;})
		.attr('fill',function(d,i){
			if(d != ''){

			calendarplot.append('text').classed('starttimetwo'+i,true)
					.attr('x',function(){ return i*(width/6+7)+10;})
					.attr('y',function(d){return height/2 + height/8+20;})
					.text(starts[i].substr(11,18))
					.attr('fill','white');

			calendarplot.append('text').classed('endtimetwo'+i,true)
					.attr('x',function(){ return i*(width/6+7)+10;})
					.attr('y',function(d){return height/2 + 2*height/8+20;})
					.text(ends[i].substr(11,18))
					.attr('fill','white');




			return 'green';}
			else{return '#FFC5A7';}
		})
		.attr('rx',12);






}

createCalendar();
