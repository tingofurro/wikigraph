<?php
include_once('../dbco.php');
$r = mysql_query('SELECT * FROM wg_category WHERE `name` IN("Theorems_in_combinatorics", "Additive_combinatorics", "Algebraic_combinatorics", "Combinatorial_algorithms", "Combinatorial_game_theory", "Combinatorialists", "Combinatorics_on_words", "Design_theory", "Enumerative_combinatorics", "Factorial_and_binomial_topics", "Graph_theory", "Incidence_geometry", "Integer_sequences", "Matroid_theory", "Permutations", "Q-analogs", "Ramsey_theory", "Set_families", "Sieve_theory", "Sparse_matrices", "Special_functions", "Sumsets", "Combinatorics_stubs")');
$re = mysql_fetch_array($r);
?>