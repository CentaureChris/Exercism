export class GradeSchool {
  private schoolRoster: Record<number, string[]> = {};

public add(student: string, grade: number): void {
    for (const g in this.schoolRoster) {
      const arr = this.schoolRoster[g];
      const idx = arr.indexOf(student);
      if (idx !== -1) {
        arr.splice(idx, 1);
        if (arr.length === 0) {
          delete this.schoolRoster[Number(g)];
        }
        break;
      }
    }

    if (!this.schoolRoster[grade]) {
      this.schoolRoster[grade] = [];
    }
    if (!this.schoolRoster[grade].includes(student)) {
      this.schoolRoster[grade].push(student);
      this.schoolRoster[grade].sort();
    }
  }

  public roster(): Record<number, string[]> {
    const clone: Record<number, string[]> = {};
    for (const [g, names] of Object.entries(this.schoolRoster)) {
      clone[Number(g)] = [...names]; 
    }
    return clone;
  }

  public grade(grade: number): string[] {
    return this.schoolRoster[grade] ? [...this.schoolRoster[grade]] : [];
  }
}
