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
          system.player.start_load();
      }
      
      if(typeof(CKEDITOR) != 'undefined'){
          var elements = document.querySelectorAll('textarea[data-editor]');
          if(elements)
              elements.forEach(function(element){
                  CKEDITOR.replace(element.name, {
                      filebrowserUploadUrl: '/uploader.php'
                  });
              });
      }
  };