$(document).ready(function() {

		parse_url();
		set_watermark();		
		load_lists();
		setup_list();
		
		$("#search-page").hide();

    $("#search-submit").click(function(e) {
        e.preventDefault();
    		var category = $("#search-select").val();
    		var keywords = $("#search-text").val();
				var page = 1;
		
				var link = "home.html"+ '?cat=' + category + '&keywords=' + keywords + '&page=' + page;
    		location.href=link;
   });
});

function parse_url() {
    var vars=[], hash;
    var hashes = window.location.href.slice(window.location.href.indexOf('?')+1).split('&');
    
    if(window.location.href.indexOf('?') > -1)  {
			for(var i = 0; i < hashes.length; i++) {
	    	hash = hashes[i].split('=');
	    	vars.push(hash[0]);
	    	vars[hash[0]] = hash[1];
	    
			}
			$("#search-select").val(unescape(vars['cat']));
			$("#search-text").val(unescape(vars['keywords']));			
	
			searchItem(vars['cat'], vars['keywords'], vars['page']);
    }
}

function searchItem(category, keywords, page) {
     $("#search-result").html("");
    $("#loading").html('<img src="images/ajax-loader.gif">');
    
    $.getJSON(
    	"data/searchItem.php",
    	{cat:category, keywords:keywords, page:page},
    	function(json) {
    		 $("#loading").html('');
        	        
         if(json.status == 'FAILED') {
            alert('searchItem failed');
         } 
         else if (json.status == 'NA') {
         		alert('no result found');
         }
         else {         
           	displaySearchResult(json.data, page, category);
						displayPages(category, keywords, json.totalpages, page);
         }
      });
}

function displaySearchResult(data, page, category) {
		
	if(category == 'Books' || category == 'Music') {	
    var str = '';
    var $i = 1;
    $.each(data, function(i, obj) {
        
	    str += '<div class="entry">';
  	  str += '<label class="itemNo">'+((page-1) * 10 + $i)+'</label>';
    	if(obj.image == '') {
      	  str += '<img src="images/no-image.jpg" />';    
    	} else {
    		str += '<img src="'+ obj.image +'" />';
    	}
    	str += '<div class="itemData">';
    	str += '<h3><a href="item.html?item='+ obj.asin +'&type='+ obj.itemtype+'">'+ obj.title+ '</a></h3> by ' + obj.author;
    	str += '<br/><div>Price:<span class="price">' + obj.price + '</span></div>';
    	str += '<br/><div>Rating:<span class="rating">' + obj.rating+ '</span></div>';
    	str += '</div>';
    	str += '</div>';
    
    	$i++;
    });
  }
  else if(category == 'Video') {
  	
    var str = '';
    var $i = 1;
   
    $.each(data, function(i, obj) {
			//alert(data);
			
    	str += '<div class="entry">';
    	str += '<label class="itemNo">'+((page-1) * 10 + $i) + '</label>';
    	str += '<img src="' + obj.thumbnailUrl + '" style="clear:right;"/>';
    	str += '<div class="itemData">';
    	str += '<h3><a href="item.html?item='+obj.videoId+'&type='+ category +'">'+obj.videoTitle+'</a></h3>';
    	str += '<br>' + obj.videoDescription + '</br>';
    	str += '</div></div>';
    	
    	$i++;    	
    });   
  }
  else if(category == 'Photo') {
  	var str = '';
    var $i = 1;

  	$.each(data, function(i, obj) {
  		
	    str += '<div class="entry">';
  	  str += '<label class="itemNo">'+((page-1) * 10 + $i)+'</label>'; 
  	  if(obj.image == '') {
      	  str += '<img src="images/no-image.jpg" />';    
    	} else {
    		str += '<img src="'+ obj.image +'" />';
    	}
    	str += '<div class="itemData">';
    	str += '<h3><a href="item.html?item='+ obj.id +'&type='+ category+'">'+ obj.title+ '</a></h3> by <a href="http://flickr.com/photos/' + obj.author + '">'+obj.author+'</a>';
    	str += '</div>';
    	str += '</div>';
    
    	$i++;      	
  	});
  }
  else {}
  	
  $("#search-result").html(str);
}

//display page navigation links
// this should be sepearate into its own file or use a jquery pagination plugin
function displayPages(category, keywords, totalpages, currentpage) {
	$("#search-page").show();
	
  var str = '';
  var link = 'home.html?cat=' + category +'&keywords='+keywords+'&page=';
  var tmp = '';
  
  if(totalpages*1 <= 12) {
  	//display all of them
		for (var i = 1; i <= totalpages*1; i++) {
	    tmp = link+i;
	    
	    if(currentpage == i) {
				str += '<a href='+tmp+' class="current_page">' +i+'</a> ';				
	    } else {
				str += '<a href='+tmp+'>' +i+'</a> ';
	    }	    
		} 	
  }
	//test with lanai(20), suzhou(19), hangzhou(21), hainan(24), wuhan(35)
  		else {
	  	if(currentpage <= 10) {
  		
  	 		for(var i = 1; i <= 10; i++) {
					tmp = link+i;
			
					if(currentpage == i) {
		    		str += '<a href='+tmp+' class="current_page">' +i+'</a> ';
					} else {
			    	str += '<a href='+tmp+'>' +i+'</a> ';
					}
	  		}	    
	  		str += '...';
	  	
		  	for(var i=totalpages-1; i<=totalpages; i++) {
		  		tmp = link+i;
			    str += '<a href='+tmp+'>' +i+'</a> ';
	  		}	  	
  		}
  		else if (currentpage > totalpages-10 && currentpage <= totalpages) {
	  		//previous 2 pages
	   		for(var i = 1; i <= 2; i++) {
					tmp = link+i;
		  	  str += '<a href='+tmp+'>' +i+'</a> ';
		  	}	    
		  	str += '...';
	  	
		  	//last 10 pages
		  	for(var i= totalpages-9; i<=totalpages; i++) {
		  		tmp = link+i;
	  		
					if(currentpage == i) {
			    	str += '<a href='+tmp+' class="current_page">' +i+'</a> ';
					} else {
				    str += '<a href='+tmp+'>' +i+'</a> ';
					}	  					  			
			}  			
  	}
	  	else {
	  	//previous 2 pages
   		for(var i = 1; i <= 2; i++) {
				tmp = link+i;
	  	  str += '<a href='+tmp+'>' +i+'</a> ';
	  	}	    
	  	str += '...';
	  	
	  	// 10 pages in the middle
	  	var startindex = parseInt((currentpage-1) / 10) *10; 
	  	for (var i = startindex+1; i <= startindex + 10; i++) {
				tmp = link+i;
			
				if(currentpage == i) {
		    	str += '<a href='+tmp+' class="current_page">' +i+'</a> ';
				} else {
			    str += '<a href='+tmp+'>' +i+'</a> ';
				}		  		
	  	}
	  	str += '...';
	  	
	  	//last 2 pages
	  	for(var i= totalpages-1; i <=totalpages; i++) {
	  		tmp = link+i;
	  		 str += '<a href='+tmp+'>' +i+'</a> ';
	  	}	  		  	
	  }  	   	
  }
  $("#pageNumber").html(str);
        
  //previous page and next page go here.
	if(currentpage ==1) {
	    $("#previous_page").attr("disabled", true);
	    $("#previous_page").click(function() { return false; });
	    
	    if(totalpages == 1) {
	    	$("#next_page").attr("disabled", true);
	    	$("#next_page").click(function() { return false; }); 
	    } else {
	    	$("#next_page").attr("href", link+(currentpage*1+1));
	    }
			
	} else if (currentpage == totalpages) {
      $("#next_page").attr("disabled", true);
	    $("#next_page").click(function() { return false; }); 
	    
	    $("#previous_page").attr("href", link+(currentpage*1-1));
	} else {
			$("#previous_page").attr("href", link+(currentpage*1-1));
	    $("#next_page").attr("href", link+(currentpage*1+1));
	}    
}
