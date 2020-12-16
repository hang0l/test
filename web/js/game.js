let s = Snap(800, 600);

let rect0 = s.rect(0, 0, 800, 600);

rect0.attr({
	fill: 'gray'
});

let x = 10;
let y = 10;
let rect = s.rect(x, y, x+50, y+50);


let rect2 = s.rect(50, 50, 50, 50);

rect2.drag()

rect.drag()