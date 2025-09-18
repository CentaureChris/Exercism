export class Dice {
  public static value: number;

  constructor(){
  }

  public static throwDice = ():number => {
    return Math.floor(Math.random() * 6) +1;
  }
}

export class DnDCharacter {

  public strength: number;
  public dexterity: number;
  public constitution: number;
  public intelligence: number;
  public wisdom: number;
  public charisma: number;
  public hitpoints: number;

  constructor(){
     this.strength = DnDCharacter.generateAbilityScore();
     this.dexterity = DnDCharacter.generateAbilityScore();
     this.constitution = DnDCharacter.generateAbilityScore();
     this.intelligence = DnDCharacter.generateAbilityScore();
     this.wisdom = DnDCharacter.generateAbilityScore();
     this.charisma = DnDCharacter.generateAbilityScore();
     this.hitpoints = 10 + DnDCharacter.getModifierFor(this.constitution);

  }

  public static generateAbilityScore(): number {
    const throwsValue = [Dice.throwDice(),Dice.throwDice(),Dice.throwDice(),Dice.throwDice()]
    .sort((a, b) => b - a)
    .slice(0, 3) 
    .reduce((acc, element) => acc + element,0);

    return throwsValue;
  }

  public static getModifierFor(abilityValue: number): number {
    const value = (abilityValue - 10)/2
    return Math.floor(value)
  }
}



