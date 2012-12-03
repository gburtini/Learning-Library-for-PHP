-- O(n^4) matrix multiplication
-- select A.row as row, B.col as col, sum(A.val * B.val) as val
-- from A, B where A.col = B.row
-- group by A.row, B.col;


--	-- we're stuck without matrix inversion here.
-- ****** the equation we're trying to solve is B = (X'X)^-1 X' Y *****
-- where X is the matrix [1, X, X2, X3, X4, ... , Xn]

-- possible solution: one can compute the cofactor matricies by selecting from this where row/col do not have a value
-- if you can compute cofactors, you can compute an adj(X) -- the inverse, A^-1 = 1/det(a) * adj(a)
-- to get adj(A), follow these steps:
-- 		compute cofactor for each cell by removing row i and col j.
--		compute the determinant of that.
-- 		multiply it by -1^(i+j)
-- this is the new row i, col j of your adj(A) matrix
-- but we're stuck trying to compute determinants.


-- The below computes (X' X) and leaves the resulting values as xm
-- add an x0 that is all 1s to have an intercept term
SELECT
	Xt.row as row, X.col as col, sum(Xt.xm * X.xm) as xm
FROM

	(SELECT x as xm, 1 as row, ID as col FROM a 
		UNION ALL 
	SELECT x2 as xm, 2 as row, ID as col FROM a) as X,


	-- transpose the matrix by swapping rows/columns
	(SELECT x as xm, 1 as col, ID as row FROM a 
		UNION ALL 
	SELECT x2 as xm, 2 as col, ID as row FROM a) as Xt

WHERE 
	Xt.col = X.row

GROUP BY
	Xt.row, X.col;

-- 2x2 determinant
SELECT SUM(val * ival * sign) as det FROM
	(SELECT 
	   main.row, main.col, main.val, interm.val as ival, POWER(-1, ID) as sign
	FROM 
		(SELECT val, row, col, (row+col) as ID FROM a WHERE row = 1) as interm,
		a as main
	WHERE
		main.row != interm.row AND main.col != interm.col) as intermediary

