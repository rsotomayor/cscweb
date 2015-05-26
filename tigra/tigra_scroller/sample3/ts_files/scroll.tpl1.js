var 
LOOK = {
	// scroller box size: [width, height]
	'size' : [300, 225],
	// for on-demand mode: path to image for scrolling items backward 
	// if relative than to Tscroll_path_to_files
	'up' : 'alf.gif', 
	// for on-demand mode: path to image for scrolling items forward 
	// if relative than to Tscroll_path_to_files
	'dn' : 'art.gif'
},

BEHAVE = {
	// if scrolling mode is auto (true / false); 
	'auto'  : false, 
	// if scrolling direction is vertical (true / false, false means horisontal)
	'vertical' : false, 
	// scrolling speed, pixels per 40 miliseconds;
	// for auto mode use negative value to reverse scrolling direction
	'speed' : 10
},
// a data to build scroll window content
ITEMS = [
	{	// file to get content for item from; if is set 'content' property doesn't matter
		// only body of HTML document is taken to become scroller item content
		// note: external files require time for loading 
		// it is RECOMMENDED to use content property to speed loading up
		// please, DON'T forget to set ALL IMAGE SIZES 
		// in either external file or in 'content' string for scroller script 
		// to be able to estimate item sizes
		'file' : '',
		// string to be displayed as item content, 
		// is RECOMMENDED to be used as an alternative to 'file' property
		'content' : '<b><center>Tigra Scroller Advantages</center></b>',
		// pause duration when item top gets top of the scroller box, seconds
		'pause_b' : 1,
		// pause duration when item bottom gets bottom of the scroller box, seconds
		'pause_a' : 0
	},
	{
		'file': '',
		'content': '<p><b>Saves the space on the page</b> - With Tigra Scroller there is always enough space for the content you want on your site. Hot news, events, slide shows, ads - everything can fit without destroying your design idea.</p>',
		'pause_b': 1,
		'pause_a': 0
	},
	{
		'file' : '',
		'content' : '<p><b>Attracts visitor\'s attention</b> - Make sure your visitors read hot content immediately after page loads. With Tigra Scroller text and images presented in a natural way for easy reading.</p>',
		'pause_b' : 1,
		'pause_a' : 0
	},
	{
		'file' : '',
		'content' : '<p><b>Professional Look</b> - With this simple to install and maintain widget your site can look really cool.</p>',
		'pause_b' : 1,
		'pause_a' : 0
	}
]
