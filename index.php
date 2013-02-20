<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">

		<title>Fallowing Words</title>
		<link rel="shortcut icon" href="../images/favicon.ico">
		<link rel="stylesheet" type="text/css" href="../../css/reset.css" /> 
		<link rel="stylesheet" type="text/css" href="../../css/bootstrap-2.0.2.css"/>
		<link rel="stylesheet" type="text/css" href="css/fallowingwords.css"/>

	

		<!-- HTML5 Tags for IE 9 -->
		<!--[if lt IE 9]>
			<script src="../js/html5-ie.js"></script>
		<![endif]-->


	</head>	
	<body>


		<div class="container-fluid">

			<div class="row-fluid">

				<div class="span9"> <h1 class="span10">Fallowing Words</h1> </div> 
				<div class="span3">
					<form id="queryBox" class="form-search">
				  		<input id="searchBox"  class="input-large ">
				    	<a id="searchButton" target="_blank" class="btn btn-mini" >Search</a>
					</form>
				</div>
			</div>


			<div class="row-fluid">
				<div id="left" class="span10">
						
						<div id="fall" >
						</div>

				</div>
				<div id="right" class="span2">
					<div id="saw-list">
						<ul>

						</ul>
 
 					</div>	
				</div>
			</div>
		</div>	
		<div class="modal hide fade" id="moreinfo">
		<div class="modal-header">
			<a class="close" data-dismiss="modal">×</a>
		</div>
		<div class="modal-body">
			
		</div>
		<div class="modal-footer">
			<a href="#" data-dismiss="modal" class="close btn ">Close</a>
		</div>
	</div>
		



	</body>
	<script type="text/javascript" src= "https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
	<script type="text/javascript" src= "../../js/jquery.pause.min.js"></script> 
	<script type="text/javascript" src= "../../js/bootstrap-tooltip.js"></script> 
	<script type="text/javascript" src= "../../js/bootstrap-popover.js"></script> 
	<script type="text/javascript" src= "../../js/bootstrap-alert.js"></script> 
	<script type="text/javascript" src= "../../js/bootstrap-modal.js"></script> 
	<script type="text/javascript" src= "../../js/bootstrap-transition.js"></script> 
	<script type="text/javascript" src= "../../js/jquery.color.js"></script>
	<script type="text/javascript">
		var doc = $(document);
		doc.ready(function() 
		{	


			var li_div_a_cache = $("li div a");	
			li_div_a_cache.live("hover",  function(){
				li_div_a_cache.trigger('mouseenter');
			});

			var li_div_a_cache = $("li div a");	

			li_div_a_cache.live("click",  function(){
				getMoreInfo($(this).attr('id'));
						
			});
					
			var height = doc.height();
			var t = parseInt($("#left").css("padding-top"));
			$("#left").css( {"height": height*4/5-t } );
			$("#right").css( {"height": height*4/5-t} );

			var fall = $("div#fall");
			var apart = 240;
			var numWords = Math.round(parseInt(fall.width())/apart);	
			
			for(var i = 0; i < numWords; i++)
			{
				getRandom(fall, i*apart+90);
			}

			function getDefinition(word, offset)
			{
				$.getJSON("word_definition.php", 
					{
						"data": word
					},
					function(result)
					{
		
						var divWordCache = $('div#'+word);
						$('div#' + word+offset).live("hover",  function(){
							var options = {placement: 'bottom', title: word, content: result, selector: 'div#' + word+offset + ' a'};

							$('div#' + word+offset).popover(options);
							
						});
						$('div#' + word+offset + ' a').attr({'data-content': result, 'data-original-title': word});
						divWordCache.trigger('mouseenter');

					}
				);
			}

			function getMoreInfo(word)
			{
				$.getJSON("get_more_info.php", 
					{
						"data": word
					},
					function(result)
					{
						$('#moreinfo').modal({
 							keyboard: true,
							show: true
						});
						$("div.modal-header").empty();
								
						$("div.modal-header").html("<a class=\"close\" data-dismiss=\"modal\">×</a>");
						$("div.modal-header").append("<h1>More about <span class=\"dark-blue\"> " + word + " </span> </h1>");
							
						$("div.modal-body").empty();
						$("div.modal-body").append("<p> <h2><strong>Definition:</strong></h4> </p>");
						$("div.modal-body").append("<p> <h4>"+result[0]+"</h3> </p>");
						$("div.modal-body").append("<p> <h2><strong>Sample Sentence:</strong></h4> </p>");
						$("div.modal-body").append("<p> <h4>"+result[1]['text']+"</h2> </p>");
						console.log(result);
	

					}
				);



			}	

			function getRandom(fall, offset)
			{
				$.getJSON("random_words.php", 
					{
					},
					function(result){
						createWord(fall, result, offset);
						getDefinition(result, offset);
					}
				);
			}

		
			function createWord(fall, word, offset)
			{
				fall.append("<div id="+word+offset+" class=\"absolute\" > <a id="+word+" class=\"word\" rel=\"popover\"> " + word + " </a> </div>");
				$('div#'+word+offset).css( {"left": offset } );
				animation(fall, word+offset, offset);
			}

			function animation(fall, idTag, offset)
			{
				var fallHeight = parseInt(fall.css("height"))*.80;
				var div_word = $("div#"+idTag);
				var word = $("div#"+idTag + " a");
				var sawList = $("div#saw-list ul");
				word.animate({ backgroundColor: get_random_color(), top: fallHeight }, 
					6000, 
					function() //recurse
					{ 
						div_word.fadeOut("linear"); 
						getRandom(fall, offset);
						word.removeClass().addClass('list-word');
						sawList.append( "<li> <div id="+div_word.attr('id')+">"+ div_word.html() +  "</div> </li>");
						word.remove();
						div_word.remove();
					}); 

				word.hover(
					function()
					{
						word.pause();
					},
					function()
					{
						word.resume();
					}
				);
			}
		
			$(window).keydown(function(event){
				if(event.keyCode == 13) {
					event.preventDefault();
					search();
					return false;
				}
			});
			$("#searchButton").click(
				function()
				{
					search();	
				}
			);


			function search()
			{

				if($('#searchBox').val() != ''){
						$('#searchButton').attr('href', "http://www.merriam-webster.com/dictionary/"+$('#searchBox').val());
						$('#searchButton').trigger("click");

				}
			}

			function get_random_color() 
			{
				var letters = '0123456789ABCDEF'.split('');
				var color = '#';
				for (var i = 0; i < 6; i++ ) {
					color += letters[Math.round(Math.random() * 15)];
				}
				return color;
			}

			function get_random_number() 
			{
				var letters = '0123456789ABCDEF'.split('');
				var color = '';
				for (var i = 0; i < 6; i++ ) {
					color += letters[Math.round(Math.random() * 15)];
				}
				return color;
			}


		});


	

	</script>

</html>
