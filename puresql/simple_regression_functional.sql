SELECT		-- computes the simple regression of x on y in table a (all references come from the inner-most query)
	(
		ybar - beta * xbar
	) AS alpha, beta

FROM (
	SELECT 
		(
			(xy_bar - xbar_ybar) / ( x2bar - xbar2 )
		) AS beta, xbar, ybar
	FROM (
		-- this is the only subquery that runs on actual data (the rest all run
		-- on one row)
		SELECT 
			AVG( x * y ) AS xy_bar, 
			AVG( x ) * AVG( y ) AS xbar_ybar, 
			AVG( POW( x, 2 ) ) AS x2bar, 
			POW( AVG( x ) , 2 ) AS xbar2, 
			AVG( y ) AS ybar, 
			AVG( x ) AS xbar
		FROM a
	) AS intermediaries
) AS coefficientintermediaries
