t = 1:4;

m1 = [0.37, 0.34, 0.28, 0.35];
m2 = [0.42, 0.37, 0.34, 0.43];
m3 = [0.23, 0.226, 0.21, 0.24];

hold on
plot(t, m1, 'r');
plot(t, m2, 'b');
plot(t, m3, 'b-');
legend('Links', 'NLP KNN', 'NLP Eps');
hold off