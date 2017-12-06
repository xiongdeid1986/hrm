var doc=document;
var date=new Date();
function Calendar(){
	this.init.apply(this,arguments);
}
Calendar.prototype={
	init:function(tableId,dateId,selectY,selectM,url){
		var table=doc.getElementById(tableId);
		this.url=url;
		this.selectY=selectY;
		this.selectM=selectM;
		if(url){
			this._showCalendarAjax(table,dateId,this.selectY,this.selectM-1);
		}else{
			this._showCalendar(table,dateId,this.selectY,this.selectM-1);
		}
		this._clickBtn(table,dateId);
	},
	//鼠标移入移出日期
	_mouseOn:function(obj){
		$(obj).mouseover(function(){
				$(obj).addClass('yellow');
		});
		$(obj).mouseout(function(){
				$(obj).removeClass('yellow');;
		});
	},
	_clickBtn:function(table,dateId){
		var _this=this,year=0;
		var btn=doc.getElementById(dateId).getElementsByTagName('a');
		btn[0].onclick=function(){
			_this.selectY--;
			if(_this.url){
				_this._showCalendarAjax(table,dateId,_this.selectY,_this.selectM-1);
			}else{
				_this._showCalendar(table,dateId,_this.selectY,_this.selectM-1);
			}
		}
		btn[1].onclick=function(){
			_this.selectM--;
			if(_this.selectM<=0){
				_this.selectM=12;
				_this.selectY--;
			}
			if(_this.url){
				_this._showCalendarAjax(table,dateId,_this.selectY,_this.selectM-1);
			}else{
				_this._showCalendar(table,dateId,_this.selectY,_this.selectM-1);
			}
		}
		btn[2].onclick=function(){
			_this.selectM++;
			if(_this.selectM>12){
				_this.selectM=1;
				_this.selectY++;
			}
			if(_this.url){
				_this._showCalendarAjax(table,dateId,_this.selectY,_this.selectM-1);
			}else{
				_this._showCalendar(table,dateId,_this.selectY,_this.selectM-1);
			}
		}
		btn[3].onclick=function(){
			_this.selectY++;
			if(_this.url){
				_this._showCalendarAjax(table,dateId,_this.selectY,_this.selectM-1);
			}else{
				_this._showCalendar(table,dateId,_this.selectY,_this.selectM-1);
			}
		}
	},
	//显示日历
	_showCalendarAjax:function(table,dateId,year,month){
		var _this = this;
		$.get(_this.url+'&year='+year+'&month='+month,function(data){
			_this._showCalendar(table,dateId,year,month,data);
			$('.calendar_tips').popover({
				html:true,
				trigger:'click'
			}); 
		},'json');
	},
	_showCalendar:function(table,dateId,year,month,data){
		$('#'+dateId+' .selectY').html(year);
		$('#'+dateId+' .selectM').html(month+1);
		var date=new Date();
		var _year=date.getFullYear();
		var _month=date.getMonth();
		var _date=date.getDate();
		date.setYear(year);
		date.setMonth(month);
		date.setDate(1);
		var day=date.getDay();
		var _this=this;
		var monthDays=this._getMonthDays(year,month);
		var td=table.getElementsByTagName('td');
		for(var k=0;k<td.length;k++){
			td[k].innerHTML="";
			td[k].className="";
		}
		for(var i=day,len=td.length;i<len;i++){
			var _td=td[i];
			var j=i-day+1;
			if(data && data[j] != null){
				$(_td).addClass("red date");
				_this._mouseOn(_td);
				var c_tips = '<a href="'+data[j]['url']+'" title="'+data[j]['title']+'" ';
				if(data[j]['tips']){
					c_tips += 'class="calendar_tips" data-content="';
					var k = 0;
					if(data[j]['tips']['task']){
						c_tips +='任务：';
						while(data[j]['tips']['task'][k]){
							c_tips += '<a href=\''+data[j]['tips']['task'][k]['url']+'\'>'+data[j]['tips']['task'][k]['name']+'</a>';
							k++;
						}
					}
					var k = 0;
					if(data[j]['tips']['event']){
						c_tips +='日程：';
						while(data[j]['tips']['event'][k]){
							c_tips += '<a href=\''+data[j]['tips']['event'][k]['url']+'\'>'+data[j]['tips']['event'][k]['name']+'</a>';
							k++;
						}
					}
					
					c_tips += '"';
				}
				c_tips += '><img src="./Public/img/calender.png">'+j+'</a>';
				$(_td).html(c_tips);
			}else{
				$(_td).html(j);
			}
			if(!data){
				_this._mouseOn(_td);
				$(_td).addClass("date");
			}
			if(_year==year&&_month==month&&_date==j){
				$(_td).addClass("today");
			}
			
			if(j>=monthDays){
				break;
			}
		}
	},
	//返回某个月的天数
	_getMonthDays:function(year,month){
		var monthAry=[31,28,31,30,31,30,31,31,30,31,30,31];
		if(year%400==0){
			monthAry[1]=29;
		}else{
			if(year%4==0&&year%100!=0){
				monthAry[1]=29;
			}
		}
		return monthAry[month];
	}
}