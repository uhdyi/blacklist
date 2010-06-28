$(document).ready(function() {
    var itemId = '';
    var itemType = '';    
    var vars=[], hash;
    
    var hashes = window.location.href.slice(window.location.href.indexOf('?')+1).split('&');
    if(window.location.href.indexOf('?') > -1)  {
        for(var i = 0; i < hashes.length; i++) {
            hash = hashes[i].split('=');
            vars.push(hash[0]);
            vars[hash[0]] = hash[1];
        }
    }   
    itemId = vars['item'];
    itemType = vars['type'];
    
    set_watermark();
    var listNames = load_lists();   		
 		setup_list(); 
		
    $("#search-submit").click(function(e) {
        e.preventDefault();
        var category = $("#search-select").val();
        var keywords = $("#search-text").val();
        
        var link = "home.html"+ '?cat=' + category + '&keywords=' + keywords + '&page=1';
        location.href=link;
    });
        
 		getItemDetail(itemId, itemType, listNames);
});

function getItemDetail(itemId, itemType, listNames) {
    $("#loading").html('<img src="images/ajax-loader.gif">');
	
	$.getJSON(
		"data/getItemDetail.php",
		{itemId: itemId, type:itemType},
		function(json) {
			$("#loading").html('');
			if(json.status == 'FAILED') {
				alert(json.statusmsg);
			} else {
				displayItem(json.data, itemType, listNames);
			}
		});
}

function displayItem(data, itemType, listNames) {
	
	if(itemType == 'Books' || itemType == 'Music') { 
		displayItemDetails(data, listNames, itemType);
  } 
  else if (itemType == 'Video') {
  	displayVideoDetails(data, listNames);
  }
  else if (itemType == 'Photo') {
  	displayPhotoDetails(data, listNames);
  }  
  else {
  	//
  }
}

function displayItemDetails(data, listNames, itemType) {
    var title = '';
    var author = '';
    var price = '';
    var rating = '';
		        
    $.each(data, function(i, obj) {
    	
    	title = obj.title;
    	author = obj.author;
    	price = obj.price;
    	rating = obj.averating;

			var optStr = '';
			$.each (listNames, function(i, lname) {
				optStr += '<option>'+ lname + '</option>';
			}); 
		  
	if(obj.image == '') {
	    $("#book-cover").html('<img src="images/no-image.jpg" width="250" height="300">');
    	} else {
	    $("#book-cover").html('<img src="'+ obj.image +'" width="250" height="300">');    		
    	}
    	$("#book-title").html('<h3>' + title + '</h3>');
    	$("#book-author").html('by ' + author);
    	$("#book-price").html('Price:' + price);
    	$("#book-rating").html('Rating:' + rating);

    	var urlStr = '';
    	urlStr += 'Add to List:<select id="favorite-list">' + optStr + '</select>';
    	urlStr += '<a href="#" class="addItem">Add</a></div>';
	    $("#addToList").html(urlStr);
	    
	    if(itemType == 'Books') {
				urlStr = '';
				urlStr += '<a><img src="http://code.google.com/apis/books/images/gbs_preview_button1.gif" border="0" style="margin: 0"/></a>';
    		$("#googleBrowse").html(urlStr);        	
    		
    		var cover_url = "http://books.google.com/books?vid=ISBN" + obj.itemId + "&printsec=frontcover#v=onepage&q&f=false";
 				$("#googleBrowse a").attr("href", cover_url);
			}
			$("#book-description").html('<h3>Amazon Description</h3><p>' + obj.description + '</p>');
		
			var str = '';
			str += '<h3>Customer Reviews</h3><dl>';
      $i = 0;
      $.each(obj.review, function(key, value) {
            var star = 'star' + $i;
            str += '<dt>' + value.rating + ' <span class="summary">' + value.summary + '</span>';
            str += '<span class="commentor">' + value.date + ' by ' + value.reviewer + ' from ' + value.location + '</span></dt>';
            str += '<dd>' + value.content + '</dd>';
            
            $i++;
        });
			str += '</dl>';
			$("#book-reviews").html(str);	
    });
    
    $("#item-detail").find('dd').hide().end().find('dt').click(function() {
       $(this).next().slideToggle();       
    });

		$("#addToList .addItem").live('click', function() {
        var list = $("#favorite-list").val();
        addToList(list, title, author, price, rating);
    });      
}

function displayVideoDetails(data, listNames) {
    var title = data.title;
    var author = data.author;
    var price = '';
    var rating = data.rating;

		var tmp = data.videoUrl + '&autoplay=1';
		var videoUrl='<object width="425" height="350"><param name="movie" value="'+tmp+'"></param><param name="wmode" value="transparent"></param><embed src="'+tmp+'" type="application/x-shockwave-flash" wmode="transparent" width=425" height="350"></embed></object>';
		$("#book-cover").html(videoUrl);
		
		$("#book-details").removeClass("row1-right-big");
		$("#book-details").addClass("row1-right");
		
		$("#book-title").html('<h3>' + title + '</h3>');
		$("#book-author").html('by <a href="'+ data.authorUrl+'">' + author +'</a>');
		$("#book-rating").html('Rating: ' + rating + ' (' + data.numRaters+' ratings)');

		var optStr = '';
			$.each (listNames, function(i, lname) {
				optStr += '<option>'+ lname + '</option>';
		}); 

    var urlStr = '';
    urlStr += 'Add to List:<select id="favorite-list">' + optStr + '</select>';
    urlStr += '<a href="#" class="addItem">Add</a></div>';
	  $("#addToList").html(urlStr);					    	
		
		$("#book-description").html('<h3>Description:</h3> ' + '<p>' + data.description + '</p>');
		$("#video-tags").html('<b>Tags:</b> ' +data.tags);
		$("#video-duration").html('<b>Duration:</b> ' + data.duration + ' seconds');
		$("#view-count").html('<b>View count:</b> ' +data.viewCount);

		$("#video-url").html('<b>Flash:</b> ' + data.videoUrl);
		$("#watch-page").html('<b>Watch page:</b> <a href="' + data.watchPage+ '">'+data.watchPage+'</a>');
		
		$("#addToList .addItem").live('click', function() {
    	var list = $("#favorite-list").val();
    	addToList(list, title, author, price, rating);
    });  
}

function displayPhotoDetails(data, listNames) {
    var title = data.title;
    var author = data.author;
    var price = '';
    var rating = '';

		var imgUrl = '<img src="'+ data.image +'" width="250" height="300">';
		$("#book-cover").html(imgUrl);
		  
    $("#book-title").html('<h3>' + title + '</h3>');
		$("#book-author").html('by <a href="'+ data.authorUrl+'">' + author +'</a>');
	$("#book-rating").html('Date taken: '+data.date);

		var optStr = '';
			$.each (listNames, function(i, lname) {
				optStr += '<option>'+ lname + '</option>';
		}); 

    var urlStr = '';
    urlStr += 'Add to List:<select id="favorite-list">' + optStr + '</select>';
    urlStr += '<a href="#" class="addItem">Add</a></div>';
	  $("#addToList").html(urlStr);	
		$("#addToList .addItem").live('click', function() {
    	var list = $("#favorite-list").val();
    	addToList(list, title, author, price, rating);
    }); 
    
		$("#book-description").html('<h3>Description:</h3> ' + '<p>' + data.description + '</p>');
		$("#video-tags").html('<b>Tags:</b> ' +data.tags);
		$("#video-url").html('<b>Link on Flickr:</b> <a href="' + data.url + '">'+data.url+'</a>');	  		
}

function addToList(list, title,author,price,rating) {
    $.getJSON(
        "data/addToList.php",
        {list:list,title:title,author:author,price:price,rating:rating},
        function(json) {
            if(json.status == 'FAILED') {
                alert(json.statusmsg);
            } else {
                //alert(json.file);
            }
        }
    );   
}