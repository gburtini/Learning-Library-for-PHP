SELECT 
	((n * standard_error) / (n * Sxx - POW(Sx, 2)) as standard_error_beta,
	standard_error, alpha, beta, Sxx, n

FROM (
	SELECT 
		(1/(n*(n-2)) * (n * Syy - POW(Sy, 2) - POW(beta,2)*(n * Sxx - POW(Sx,2)) as standard_error,
		alpha, beta, Sx, Sxx, n,
	(
		SELECT
			SUM(x) as Sx, SUM(y) as Sy,
			SUM(POW(x, 2)) as Sxx, SUM(x*y) as Sxy, SUM(POW(y, 2)) as Syy,
			COUNT(a.*) as n, alpha, beta
		FROM
			a, (
		
				SELECT (
					ybar - beta * xbar
				) AS alpha, beta
	
				FROM (

					SELECT (
						(
							xy_bar - xbar_ybar
						) / ( x2bar - xbar2 )
					) AS beta, xbar, ybar
					FROM (
						SELECT 
							AVG( x * y ) AS xy_bar, AVG( x ) * AVG( y ) AS xbar_ybar, AVG( POW( x, 2 ) ) AS x2bar, POW( AVG( x ) , 2 ) AS xbar2, AVG( y ) AS ybar, AVG( x ) AS xbar
						FROM a
					) AS intermediaries
				) AS coefficientintermediaries
			) as coefficients	-- this one gives you beta hat and alpha hat; subqueries outside of this one are to compute the confidence.

	) as errorintermediaries

