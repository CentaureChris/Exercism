interface IInstruction {
  binary:string;
  instruction:string;
}

type Instruction = IInstruction

const INSTRUCTIONS: Instruction[]= [
  { binary: "00001", instruction: "wink" },
  { binary: "00010", instruction: "double blink" },
  { binary: "00100", instruction: "close your eyes" },
  { binary: "01000", instruction: "jump" },
  { binary: "10000", instruction: "Reverse the order of the operations in the secret handshake." }
]


export function commands(num: number): string[] | string {
  if (!Number.isInteger(num) || num < 0 || num > 31)
    return "commands must be number between 0 and 31";

  const bits = num.toString(2).padStart(5, "0").split("");
  const actions: string[] = [];

  if (bits[4] === "1") actions.push("wink"); 
  if (bits[3] === "1") actions.push("double blink");
  if (bits[2] === "1") actions.push("close your eyes");
  if (bits[1] === "1") actions.push("jump");
  if (bits[0] === "1") actions.reverse();

  return actions;
}

console.log(commands(25))
