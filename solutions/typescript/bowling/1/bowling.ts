type Roll1 = number | 'X';
type Roll2 = number | '/' | '';
type Frame = [Roll1, Roll2];

type R = number | 'X' | '/' | '';
type TenthFrame = [R, R, R];

export class Bowling {
  public scoreTable: Frame[];
  public currentFrame: number;        
  public currentThrow: 1 | 2 | 3;    
  public firstThrow: number | null;
  public tenthFrame: TenthFrame | null;
  public isGameOver: boolean;

  constructor() {
    this.scoreTable = [];
    this.currentFrame = 1;
    this.currentThrow = 1;
    this.firstThrow = null;
    this.tenthFrame = null;
    this.isGameOver = false;
  }

  private ensureInt0to10(pins: number) :void {
    if (!Number.isInteger(pins) || pins > 10) {
      throw new Error("Pin count exceeds pins on the lane");
    }else if (pins < 0){
      throw new Error("Negative roll is invalid")
    }
  }

  public roll(pins: number): void {
    if (this.isGameOver) throw new Error("Cannot roll after game is over");
    this.ensureInt0to10(pins);

    if (this.currentFrame < 10) {
      this.rollFrames1to9(pins);
      return;
    }
    this.rollTenth(pins);
  }

  private rollFrames1to9(pins: number): void {
    if (this.currentThrow === 2) {
      if (this.firstThrow == null) {
        throw new Error("État invalide: firstThrow manquant pour le 2e lancer.");
      }
      const remaining = 10 - this.firstThrow;
      if (pins > remaining) {
        throw new Error(`Pin count exceeds pins on the lane`);
      }
    }

    if (this.currentThrow === 1) {
      if (pins === 10) {
        const frame: Frame = ['X', ''];
        this.scoreTable.push(frame);
        this.firstThrow = null;
        this.currentThrow = 1;
        this.currentFrame += 1;
        return;
      }

      this.firstThrow = pins;
      const frame: Frame = [pins, ''];
      this.scoreTable.push(frame);
      this.currentThrow = 2;
      return;
    }

    if (this.firstThrow == null) {
      throw new Error("État invalide: firstThrow manquant pour le 2e lancer.");
    }

    const isSpare = this.firstThrow + pins === 10;
    const lastIndex = this.scoreTable.length - 1;

    this.scoreTable[lastIndex][1] = isSpare ? '/' : pins;

    this.firstThrow = null;
    this.currentThrow = 1;
    this.currentFrame += 1;
  }

  private rollTenth(pins: number): void {
    if (!this.tenthFrame) this.tenthFrame = ['', '', ''];

    const r1 = this.tenthFrame[0];
    const r2 = this.tenthFrame[1];

    if (this.currentThrow === 1) {
      if (pins === 10) {
        this.tenthFrame[0] = 'X';
        this.pushOrUpdateTenthInTable('X', '');
        this.currentThrow = 2;
        return;
      } else {
        this.firstThrow = pins;
        this.tenthFrame[0] = pins;
        this.pushOrUpdateTenthInTable(pins, '');
        this.currentThrow = 2;
        return;
      }
    }

    if (this.currentThrow === 2) {
      if (r1 === 'X') {
        this.tenthFrame[1] = pins === 10 ? 'X' : pins;
        this.pushOrUpdateTenthInTable('X', this.tenthFrame[1] as Roll2);
        this.currentThrow = 3;
        return;
      } else {
        const remaining = 10 - (r1 as number);
        if (pins > remaining) {
          throw new Error(`Pin count exceeds pins on the lane`);
        }
        const spare = (r1 as number) + pins === 10;
        this.tenthFrame[1] = spare ? '/' : pins;
        this.pushOrUpdateTenthInTable(r1 as Roll1, spare ? '/' : (pins as Roll2));

        if (spare) {
          this.currentThrow = 3;
        } else {
          this.finishGame();
        }
        return;
      }
    }

    if (this.currentThrow === 3) {
      if (r1 === 'X') {
        if (r2 === 'X') {
          this.tenthFrame[2] = pins === 10 ? 'X' : pins;
        } else if (typeof r2 === 'number') {
          const remaining = 10 - r2;
          if (pins > remaining) {
            throw new Error(`Pin count exceeds pins on the lane`);
          }
          this.tenthFrame[2] = r2 + pins === 10 ? '/' : pins;
        } else {
          throw new Error("État invalide au 10e frame.");
        }
      } else {
        if (r2 !== '/') {
          throw new Error("Pas de 3e lancer sans spare ou strike au premier lancer du 10e.");
        }
        this.tenthFrame[2] = pins === 10 ? 'X' : pins;
      }

      this.finishGame();
      return;
    }

    throw new Error("État invalide: currentThrow hors plage.");
  }

  private pushOrUpdateTenthInTable(r1: Roll1, r2: Roll2):void {
    if (this.scoreTable.length < 9) {
      throw new Error("Le 10e frame ne peut pas commencer avant les 9 premiers.");
    }
    if (this.scoreTable.length === 9) {
      this.scoreTable.push([r1, r2]);
    } else {
      this.scoreTable[9] = [r1, r2];
    }
  }

  private finishGame() :void{
    this.firstThrow = null;
    this.currentThrow = 1;
    this.currentFrame = 11; 
    this.isGameOver = true;
  }

  // ---------- SCORE ----------
  public score(): number {
    if(this.scoreTable.length === 0 || !this.isGameOver) throw new Error("Score cannot be taken until the end of the game");

    const rolls = this.flattenAllRolls(); 
    let total = 0;
    let i = 0;

    for (let frame = 1; frame <= 10; frame++) {
      const first = rolls[i];
      if (first === undefined) break; 

      if (first === 10) {
        const b1 = rolls[i + 1] ?? 0;
        const b2 = rolls[i + 2] ?? 0;
        total += 10 + b1 + b2;
        i += 1;
        continue;
      }

      const second = rolls[i + 1];
      if (second === undefined) {

        total += first;
        break;
      }

      if (first + second === 10) {

        const b = rolls[i + 2] ?? 0;
        total += 10 + b;
      } else {

        total += first + second;
      }

      i += 2;
    }

    return total;
  }

  private flattenAllRolls(): number[] {
    const out: number[] = [];

    const upto = Math.min(9, this.scoreTable.length);
    for (let f = 0; f < upto; f++) {
      const [a, b] = this.scoreTable[f];

      if (a === 'X') {
        out.push(10);
      } else {
        const first = a;
        out.push(first);

        if (b === '/') {
          out.push(10 - first);
        } else if (typeof b === 'number') {
          out.push(b);
        } 
      }
    }

    if (this.tenthFrame || this.scoreTable.length === 10) {
      const [r1, r2, r3] =
        this.tenthFrame ??
        [this.scoreTable[9][0] as R, this.scoreTable[9][1] as R, '' as R];

      if (r1 === 'X') out.push(10);
      else if (typeof r1 === 'number') out.push(r1);

      if (r2 === 'X') out.push(10);
      else if (r2 === '/') {
        const prev = out[out.length - 1] ?? 0;
        out.push(10 - prev);
      } else if (typeof r2 === 'number') out.push(r2);

      if (r3 === 'X') out.push(10);
      else if (r3 === '/') {
        const prev = out[out.length - 1] ?? 0;
        out.push(10 - prev);
      } else if (typeof r3 === 'number') out.push(r3);
    }

    return out;
  }
}
