  var s = {st:["{}"],n:0,m:0,
  clear:function(){s.st=["{}"];s.n=0;s.m=0;},
  back:function(){if(s.m>0){s.m--;return s.m;}return false;},
  forward:function(){if(s.m<s.n){s.m++;return s.m;}return false;},
  add:function(e){s.n++;s.st[s.n]=JSON.stringify(e);s.m=s.n;return s.n;}};

  var system = system || {};

  system.blog = {};
  system.blog.data = {};
  system.blog.ajax = function(path,toStack){
      if(typeof(toStack)=='number'){
          system.blog.data = JSON.parse(s.st[toStack]);
      }
      else{
          s.add(system.blog.data);
      }
      
      jQuery.ajax({
          url: mainPath + path,
          charset: 'UTF-8',
          data: system.blog.data
      }).done(function(data){document.getElementById('blog-view').outerHTML = data});
  }
  
  system.eventType_column_fill = function(target){
      jQuery.ajax({
          url: mainPath + path,
          charset: 'UTF-8',
          data: system.blog.data
      }).done(function(data){
          document.getElementById('blog-view').outerHTML = data
      });
  }
  
  system.eventType_core_paginator = function(target){
      window.location = mainPath + 'admin/' + target.closest('.paginator').dataset['module'] + '/list?page=' + encodeURI(target.dataset['page']);
  }
  
  system.eventType_blog_page = function(target){
      system.blog.data.page = target.dataset['page'];
      
      system.blog.ajax('window/blog/View');
  };

  system.eventType_blog_home = function(target){
      system.blog.data = {};
      
      system.blog.ajax('window/blog/View');
  };
  
  system.eventType_blog_tag = function(target){
      system.blog.data = {};
      system.blog.data.name = target.dataset['name'];
      system.blog.data.tag = target.innerText;

      system.blog.ajax('window/blog/View');
  }
  
  system.eventType_crad_delete = function(target,navigator){
      var id = target.dataset['id'];
      if(confirm('Удаление id ' + id)){
          var module = navigator.dataset['module'];

          jQuery.ajax({
              url: mainPath + 'admin/' + module + '/delete/' + id,
              charset: 'UTF-8',
              data: { csrf: csrf }
          }).done(function(data){
              if(data == 2 || data == 3){
                  target.parentElement.remove();
              }
          });
      }
  }
  
  system.eventType_blog_history = function(target){
      var res = s[target.dataset['method']]();
      if(typeof(res)=='boolean')
          return;
      
      system.blog.ajax('window/blog/View',res);
  }
  
  system.eventType_blog_open = function(target){
      var id = + target.closest('.blog_post').dataset['id'];
      if(!id) return;
      
      jQuery.ajax({
          url: mainPath + 'admin/blog/open/' + id,
          charset: 'UTF-8',
          data: {
              'csrf': csrf
          }
      }).done(function(data){
          if(data == '1'){
              target.innerText = 'Показать';
              target.closest('.blog_post').setAttribute('blogclosed','');
          }
          else if(data == '2'){
              target.innerText = 'Скрыть';
              target.closest('.blog_post').removeAttribute('blogclosed');
          }
      });
  }
  
  system.eventType_blog_search = function(target){
      var row = target.previousElementSibling;
      var search = row.previousElementSibling;
      if(!search.value)
          return;
      
      system.blog.data = {};
      system.blog.data.search = search.value;
      system.blog.data.row = row.value;
      
      system.blog.ajax('window/blog/View');
  }
  
  system.eventType_core_quote = function(target){
      var data = {};
      
      if(target.dataset['name']){
          data.author = target.dataset['name'];
      }

      jQuery.ajax({
          url: mainPath + 'window/quotes/Quote',
          charset: 'UTF-8',
          data: data
      }).done(function(data){document.getElementById('core_quote').outerHTML = data});
  }
  
    system.eventType_up_button = function(target){
        elem = target.closest('[liftBody] > *');
        if(!elem || !elem.previousElementSibling)
            return;
        $(elem).after(elem.previousElementSibling);
    };
    system.eventType_down_button = function(target){
        elem = target.closest('[liftBody] > *');
        if(!elem || !elem.nextElementSibling)
            return;
        $(elem).before(elem.nextElementSibling);
    };
  
  system.toggleAttr = function(elem,attr){
      if(elem.hasAttribute(attr))
          elem.removeAttribute(attr);
      else
          elem.setAttribute(attr,1);
  };
  
(function() {
  if (!Element.prototype.matches) {
      var t = Element.prototype;
      t.matches = t.msMatchesSelector||t.mozMatchesSelector||t.webkitMatchesSelector;
  }
  if (!Element.prototype.closest) {
    Element.prototype.closest = function(css) {
        var node = this;
        while (node) {
            if (node.matches(css)) return node;
            else node = node.parentElement;
        }
        return null;
    };
  }
})();

  window.onload = function(){
      document.addEventListener('click',function(event){
          var navigator = event.target.closest('[data-role="navigator"]');
          var target = event.target.closest('[data-type]');
          if(navigator && target && navigator.contains(target) && system['eventType_'+target.dataset['type']]){
              event.preventDefault();
              system['eventType_'+target.dataset['type']](target,navigator);
          }
      });
      
      if(system.player){
          system.player.top = document.getElementById('main_logo').offsetHeight;
          system.player.left = system.player.leftCount();
          system.player.start_load();
      }
      
      if(system.chatTick){
          system.chatTick();
      }
      
      if(typeof(CKEDITOR) != 'undefined'){
          var elements = document.querySelectorAll('textarea[data-editor]');
          if(elements)
              elements.forEach(function(element){
                  CKEDITOR.replace(element.name, {
                      filebrowserUploadUrl: '/uploader.php',
                      contentsCss: ['/css/ckeditor.css']
                  });
              });
      }
      
      //-----------------------------------------------------

        // var gui = new dat.GUI();
        
        // var ss = {
            // mainBackground: '#FFFFFF',
            // mainColor: '#331702',
            // mainTextSize: 0.9,
            // mainFont: 'bookos',
            
            // winBorder: true,
            // winBorderColor: '#EAF2F3',
            // winBackground: '#F7F7F7',
            
            // headBorder: true,
            // headBorderColor: '#E2E8E8',
            // headBackground: '#E5EFED',
            // headTextSize: 20,
            
            // linkAll: '#73C0C4',
            // linkAdmin: '#E16874',
            // linkElse: '#BCBDBC'
        // };
        
        // var f1 = gui.addFolder('Основной фон');
        // f1.addColor(ss,'mainBackground');
        // f1.addColor(ss,'mainColor');
        // f1.add(ss,'mainTextSize').min(0.1).max(2).step(0.01);
        // f1.add(ss,'mainFont',['bookos','Tahoma','Arial','Courier','Garamond','Georgia','Tahoma',"'Times New Roman'",'Verdana'])
        
        // var f2 = gui.addFolder('Окна');
        // f2.add(ss,'winBorder');
        // f2.addColor(ss,'winBorderColor');
        // f2.addColor(ss,'winBackground');
        
        // var f3 = gui.addFolder('Заголовки окон');
        // f3.add(ss,'headBorder');
        // f3.addColor(ss,'headBorderColor');
        // f3.addColor(ss,'headBackground');
        // f3.add(ss,'headTextSize').min(4).max(50).step(1);
       
        // var f4 = gui.addFolder('Цвета кнопок');
        // f4.addColor(ss,'linkAll');
        // f4.addColor(ss,'linkAdmin');
        // f4.addColor(ss,'linkElse');
       
        // setInterval(function(){
            
            // var res = '#container{background-color:'+ ss.mainBackground +';color:'+ ss.mainColor +';}';
            // res += '#body{font-size:'+ ss.mainTextSize +'em;}';
            // res += '#body{font-family:'+ ss.mainFont +'}';
            
            // res += '.window,nav{background-color:'+ ss.winBackground +';';
            // res += 'border:';
            // if(ss.winBorder){
                // res += '1px solid '+ ss.winBorderColor;
            // }
            // else{
                // res += 'none';
            // }
            // res += ';}';
            
            // res += 'h{background-color:'+ ss.headBackground +';';
            // res += 'font-size:'+ ss.headTextSize +'px;';
            // res += 'border:';
            // if(ss.headBorder){
                // res += '1px solid '+ ss.headBorderColor;
            // }
            // else{
                // res += 'none';
            // }
            // res += ';}';
            
            // res += '.admin_button{color:'+ ss.linkAdmin +';}';
            // res += '.blog_tag{color:'+ ss.linkAll +';}';
            // res += '.blog_open, .events_open{color:'+ ss.linkElse +';}';
            
            // document.getElementById('main_style').innerText = res;
        // },500);
        

      //-----------------------------------------------------
  };