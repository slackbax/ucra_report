import sys
import pandas as pd
from matplotlib import pyplot as plt
import numpy as np

# READ CSV FILE
folder = sys.argv[1]
file = '../upload/' + folder + '/upload.csv'

# PRE-PROCESSING
df = pd.read_csv(file, encoding='utf-16', sep='\t')
# df = pd.read_csv(file, encoding='utf-8', sep=";")

new_column = []
for i in range(len(df)):
  new_column.append(df["Fecha"][i][-2:]+"-"+df["Fecha"][i][-5:-3]+"-"+df["Fecha"][i][-10:-6]
                    +str(' ')+ df["Hora"][i])

df["eje_x"] = new_column

y_max = np.max(df["Sys"])
y_min = np.min(df["Dia"])
pa_max = np.max(df["Pulso"])
pa_min = np.min(df["Pulso"])

lista_de_dias = df["Fecha"].unique().tolist()[:-1]
mediciones_x_dia = []

for dia in lista_de_dias:
  mediciones_x_dia.append(len(df.query("Fecha == @dia")))

mediciones_x_dia_std =[]
for count, value in enumerate(mediciones_x_dia):
  if count == 0:
    mediciones_x_dia_std.append(value + 0.5)
  else:
    mediciones_x_dia_std.append(value)

mediciones_x_dia_std = list(np.cumsum(mediciones_x_dia_std))
mediciones_x_dia_std = [x-1 for x in mediciones_x_dia_std]

# BLOOD PRESSURE GRAPH

plt.figure(figsize = (16,6))
plt.title("Evolución de Presión Arterial", weight='bold', fontsize='xx-large')
plt.plot(df["Sys"], 'k', linestyle='solid', color='#dc3545')
plt.plot(df["Sys"], 'ro', label='Sistólica', color='#dc3545')
plt.plot(df["Dia"], 'k', linestyle='solid', color='#325285')
plt.plot(df["Dia"], 'bo', label='Diastólica', color='#325285')
plt.plot(df[["Sys", "Dia"]].apply(np.mean, axis=1), color='w')
plt.hlines(140, -1, len(df), linestyle='dashed', color='#dc3545')
plt.hlines(90, -1, len(df), linestyle='dashed', color='#325285')
plt.vlines(mediciones_x_dia_std, y_min, y_max, linestyle='solid', color='black')
plt.xticks(np.arange(0,len(df)), df["eje_x"], rotation = 90)
plt.fill_between(np.arange(0,len(df)), df["Dia"], df["Sys"], color='#b7b7b7', alpha=0.5)
plt.xlabel("Momento de medición", weight='bold')
plt.ylabel("[mmHg]", weight='bold')
plt.xlim(-1, len(df))

plt.tight_layout()
plt.grid()
plt.legend()

plt.savefig('../upload/' + folder + '/BP_plot.png', format='png', dpi=300)

# HEART RATE GRAPH

plt.figure(figsize = (16,6))
plt.title("Evolución de Frecuencia Cardiaca", weight='bold', fontsize='xx-large')
plt.plot(df["Pulso"], 'k', linestyle='solid', color='#5AA19C')
plt.plot(df["Pulso"], 'go', label='Latidos por minuto', color='#5AA19C')
plt.vlines(mediciones_x_dia_std, pa_min, pa_max, linestyle='solid', color='black')
plt.xticks(np.arange(0,len(df)), df["eje_x"], rotation = 90)
plt.xlabel("Momento de medición")
plt.ylabel("[BPM]")
plt.xlim(-1, len(df))

plt.tight_layout()
plt.grid()
plt.legend()

plt.savefig('../upload/' + folder + '/HR_plot.png', format='png', dpi=300)

