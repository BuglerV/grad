  var system = system || {};
  
  system.events = {};
  system.events.days = [];
  system.events.months = [];
  system.events.current = 'start';
  system.events.currentMonth = document.getElementById('events_cal_container').dataset['month'];
  system.events.checkedDay;
  system.events.monthName = [
      'Январь','Февраль','Март','Апрель',
      'Май','Июнь','Июль','Август',
      'Сентябрь','Октябрь','Ноябрь','Декабрь'
  ];
  system.events.changeMonth = function(fullMonth){
      if(typeof(system.events.months[system.events.currentMonth]) == 'undefined'){
          jQuery('.cal_checked').removeClass('cal_checked');

          system.events.months[system.events.currentMonth] = document.getElementById('calendar').outerHTML;
      }
      
      if(typeof(system.events.months[fullMonth]) !== 'undefined')
      {
          document.getElementById('calendar').outerHTML = system.events.months[fullMonth];
          if(system.events.current !== 'start'){
            var cur = document.querySelector('[data-day="'+system.events.current+'"]');
            if(cur)
              cur.classList.add('cal_checked');
          }
      }
      else
      {
          var dt = new Date(fullMonth);
          var week = dt.getDay();
          week = week ? week - 1 : 6 ;

          var month = dt.getMonth() + 1;
          var year = dt.getFullYear() + '-' + (month > 9 ? month : '0' + month);
          
          dt.setDate(32);
          var day = 32 - dt.getDate();
          
          var element = document.createElement('div');
          
          if(week){
              for(var i = 1; i <= week; i++){
                  jQuery(element).append('<div class="cal_day"> </div>');
              }
          }
          
          var currentDay;
          for(var i = 1; i <= day; i++){
              currentDay = year + '-' + (i>9?i:'0'+i);
              jQuery(element).append('<div data-day="'+ currentDay +'" class="cal_day'+ (currentDay == today ? ' cal_today' : '') + (currentDay == system.events.current ? ' cal_checked' : '') + (future_events.indexOf(currentDay)>-1?' blog_tag" data-type="events_day':'') + '">'+ i +'</div>');
          }
          
          document.getElementById('calendar').innerHTML = element.innerHTML;
      }
      
      document.getElementById('calendar').dataset['month'] = fullMonth;
      system.events.currentMonth = fullMonth;
      
      fullMonth = fullMonth.split('-');
      document.getElementById('cal_month_view').innerText = system.events.monthName[fullMonth[1] - 1] + ' ' + fullMonth[0];
  }
  system.events.changeEvents = function(data,target,day){
      document.getElementById('events_future').innerHTML = data;
      jQuery('.cal_checked').removeClass('cal_checked');
      
      if(day == 'start'){
          system.events.current = '';
          return;
      }
      
      target.classList.add('cal_checked');
      
      if(day) system.events.current = day;
  }
  system.eventType_events_month = function(target,navigator)
  {
      var way = target.innerText == '<' ? -1 : 1;
      var month = system.events.currentMonth;

      var ts = new Date(month);
      ts.setMonth(ts.getMonth() + way);
      var help = ts.getMonth() + 1;
      var fullYear = ts.getFullYear() + '-' + (help > 9 ? help: '0'+help);
      
      system.events.changeMonth(fullYear);
  }
  system.eventType_events_day = function(target){
      if(target.classList.contains('cal_checked'))
          return;

      var day = target.dataset['day'];
      
      if(day == today) return;
      
      if(!system.events.days[system.events.current])
          system.events.days[system.events.current] = document.getElementById('events_future').innerHTML;
          
      if(typeof(system.events.days[day]) !== 'undefined')
          return system.events.changeEvents(system.events.days[day],target,day);
      
      jQuery.ajax({
          url: mainPath + 'window/events/Event',
          charset: 'UTF-8',
          data: {
              'event_day' : day
          }
      }).done(function(data){
          if(data){
              system.events.changeEvents(data,target,day);
          }
      });
  }