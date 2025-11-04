class Dice {  
  constructor () {
  }

  public static roll (faces:number) :number {
    if(faces < 1) throw new Error('A roll must have minimum 1 face!')
    return Math.floor(Math.random() * faces) +1;
  }
}

export class DnDCharacter {
  public strength:number;
  public dexterity:number;
  public constitution:number;
  public intelligence:number;
  public wisdom:number;
  public charisma:number;
  public hitpoints:number;

  public constructor () {
    this.strength = DnDCharacter.generateAbilityScore();
    this.dexterity = DnDCharacter.generateAbilityScore();
    this.constitution = DnDCharacter.generateAbilityScore();
    this.intelligence = DnDCharacter.generateAbilityScore();
    this.wisdom = DnDCharacter.generateAbilityScore();
    this.charisma = DnDCharacter.generateAbilityScore();
    this.hitpoints = 10 + DnDCharacter.getModifierFor(this.constitution);
  }

  public static generateAbilityScore(): number {
     const throws = [Dice.roll(6),Dice.roll(6),Dice.roll(6),Dice.roll(6)]
      .sort((a:number, b:number) => a - b);
     throws.shift();
     return throws.reduce((a:number, b:number) => a + b, 0);
  }

  public static getModifierFor(abilityValue: number): number {
    return Math.floor((abilityValue - 10) / 2);
  }
}


