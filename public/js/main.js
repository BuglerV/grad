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
  
  system.eventType_blog_history = function(target){
      var res = s[target.dataset['method']]();
      if(typeof(res)=='boolean')
          return;
      
      system.blog.ajax('window/blog/View',res);
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
      
      if(typeof(CKEDITOR) != 'undefined'){
          var elements = document.querySelectorAll('textarea[data-editor]');
          if(elements)
              elements.forEach(function(element){
                  CKEDITOR.replace(element.name);
              });
      }
  };