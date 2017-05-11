fibo(0, 0).
fibo(1, 1).
fibo(X, Y) :- X>1,
              X1 is X-1,
              fibo(X1, Y1),
              X2 is X-2,
              fibo(X2, Y2),
              Y is Y1+Y2.

expo(_, 0, 1).
expo(B, E, V) :- E > 0, !, E1 is E -1, expo(B,E1,V1), V is B * V1.

minimo([V], V).
minimo([X,Y|R], V):-
    ( X > Y ->
        minimo([Y|R], V)
    ;
        minimo([X|R], V)
    ).

inserta([], A, [A]).
inserta([X|R], A, [X|R]) :- A =:= X, !.
inserta([X|R], A, [A,X|R]) :- A < X.
inserta([X|R1], A, [X|R2]) :- inserta(R1, A, R2).

invierte(L, R):-  invierteAux(L, [], R).
invierteAux([], V, V).
invierteAux([C|Rl], V, R):-  invierteAux(Rl, [C|V], R).

elimina([], _, []) :- !.
elimina([H|T], V, Result) :- H=:=V, elimina(T, V, Result), !.
elimina([H|T], V, [H|Result]) :- elimina(T, V, Result).

repetidos([],[]).
repetidos([H|T],[H|Out]) :-
    not(esta(H,T)),
    repetidos(T,Out).
repetidos([H|T],Out) :-
    esta(H,T),
    repetidos(T,Out), !.
    
esta(X,[X|_]).
esta(X,[_|T]) :- esta(X,T).

not(A) :- \+ call(A).