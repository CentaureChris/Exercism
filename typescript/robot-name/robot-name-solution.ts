export class Robot {

  private _name: string;
  private static usedNames = new Set<string>(); // store used names

  constructor() {
    this._name = Robot.generateName()
  }


  public get name(): string {
    return this._name;
  }

  public resetName(): void {
    const old = this._name;
    let next: string;
    do {
      next = Robot.generateName();
    } while (next === old && Robot.usedNames.has(next));
    this._name = next;
  }

  public static releaseNames(): void {
    Robot.usedNames.clear();
  }

  private static generateName(): string {
    let name: string;

    do {
      const letters =
        String.fromCharCode(65 + Math.floor(Math.random() * 26)) +
        String.fromCharCode(65 + Math.floor(Math.random() * 26));

      const digits = Math.floor(Math.random() * 1000).toString().padStart(3, '0');

      name = letters + digits;
    } while (Robot.usedNames.has(name));

    Robot.usedNames.add(name);
    return name;
  }
}

let test = new Robot;
test.name