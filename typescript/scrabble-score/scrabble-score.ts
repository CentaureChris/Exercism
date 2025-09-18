interface IValuePoint {
  value: number;
  letters: string;
}

type valuePoint = IValuePoint;

const SCORESTABLE: valuePoint[]= [
    {value: 1, letters: "A,E,I,O,U,L,N,R,S,T"},
    {value: 2, letters: "D,G"},
    {value: 3, letters: "B,C,M,P"},
    {value: 4, letters: "F,H,V,W,Y"},
    {value: 5, letters: "K"},
    {value: 8, letters: "J,X"},
    {value: 10, letters: "Q,Z"},
  ]

  
export function score(word: string | undefined): number {
  if (!word) return 0; 

  let splitWord = word.toUpperCase().split('');
  let totalScore = 0;

  splitWord.forEach((letter) => {
    const val = SCORESTABLE.find(({ letters }) =>
      letters.split(',').includes(letter)
    );

    if (val) {
      totalScore += val.value;
    }
  });

  return totalScore;
}

