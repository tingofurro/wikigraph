load matrix.dat

A = spconvert(matrix);




D = zeros(size(A));
[dim, ~] = size(A);
for i = 1:dim
    D(i,i) = sum(A(i,:));
end

% L = D-A; % Standard Laplacian
L = eye(dim)-D.^(-0.5)*A*D.^(-0.5); % Standard Laplacian

L(L==inf) = 0; L(L==NaN) = 0;
[V,lambda] = eigs(L);

eigens = [];
for i = 1:dim
    eigens = [eigens, lambda(i,i)];
end

disp = sort(V(:,800));
plot(disp)